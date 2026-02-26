<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Revenue;
use App\Models\StockAdjustment;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\ProductVariant;

class DataResetController extends Controller
{
    public function resetData(Request $request)
    {
        // Must be boss
        if (!$request->user()->isBoss()) {
            abort(403, 'Only administrators can perform this action.');
        }

        // Validate the strict confirmation phrase
        $request->validate([
            'confirmation' => ['required', 'string', 'in:DELETE']
        ], [
            'confirmation.in' => 'You must exactly type "DELETE" (all caps) to confirm.'
        ]);

        DB::transaction(function () {
            // Delete all transaction records completely. Using query()->delete() for raw SQL DELETE.
            QuoteItem::query()->delete();
            Revenue::query()->delete();
            StockAdjustment::query()->delete();
            PurchaseOrder::query()->delete();
            Quote::query()->delete();

            // Reset all stock quantities to 0
            Product::query()->update(['stock_quantity' => 0]);
            ProductVariant::query()->update(['stock_quantity' => 0]);
        });

        return redirect()->route('dashboard')->with('success', 'All business transaction data has been securely deleted. Stock quantities are reset to 0. You have a fresh start.');
    }
}
