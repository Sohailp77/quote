<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Revenue;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Manual stock adjustment (boss only).
     */
    public function adjust(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'direction' => 'required|in:add,deduct',
            'reason' => 'required|string|max:255',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'unit_cost' => 'nullable|numeric|min:0',
            'transaction_type' => 'nullable|in:adjustment,sale,loss,purchase',
            'amount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $product, $validated) {
            $quantity = (int) $validated['quantity'];
            $change = $validated['direction'] === 'add' ? $quantity : -$quantity;
            $type = $validated['transaction_type'] ?? 'manual';

            $target = $product;
            if (!empty($validated['product_variant_id'])) {
                $target = \App\Models\ProductVariant::findOrFail($validated['product_variant_id']);
            }

            $adjustment = $target->adjustStock(
                change: $change,
                type: $type,
                reason: $validated['reason'],
                userId: $request->user()->id,
                unitCost: isset($validated['unit_cost']) ? (float) $validated['unit_cost'] : null,
            );

            // Record revenue if it's an external sale
            if ($type === 'sale' && !empty($validated['amount'])) {
                Revenue::create([
                    'amount' => (float) $validated['amount'],
                    'recorded_at' => now(),
                    'quote_id' => null,
                    'stock_adjustment_id' => $adjustment->id,
                ]);
            }

            return back()->with('success', 'Stock updated and financial impact recorded.');
        });
    }

    /**
     * Update an adjustment record (boss only).
     */
    public function update(Request $request, StockAdjustment $adjustment)
    {
        Gate::authorize('boss');

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);

        $adjustment->update($validated);

        return back()->with('success', 'Adjustment record updated.');
    }

    /**
     * Revert a stock adjustment (boss only).
     */
    public function revert(Request $request, StockAdjustment $adjustment)
    {
        Gate::authorize('boss');

        if ($adjustment->reverted_at) {
            return back()->with('error', 'This adjustment has already been reverted.');
        }

        return DB::transaction(function () use ($request, $adjustment) {
            $target = $adjustment->variant ?: $adjustment->product;

            // Mark original record as reverted
            $adjustment->update(['reverted_at' => now()]);

            // Create counter-adjustment
            $target->adjustStock(
                change: -$adjustment->quantity_change,
                type: 'reversion',
                reason: "Reverted adjustment #{$adjustment->id}: {$adjustment->reason}",
                userId: $request->user()->id,
            );

            // Mark associated revenue as reverted (instead of deleting)
            Revenue::where('stock_adjustment_id', $adjustment->id)
                ->update(['reverted_at' => now()]);

            return back()->with('success', 'Adjustment reverted successfully.');
        });
    }
}
