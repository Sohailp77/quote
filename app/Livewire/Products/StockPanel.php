<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Revenue;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class StockPanel extends Component
{
    public Product $product;
    public $appSettings;
    public $isBoss;

    public $direction = 'add';
    public $transactionType = 'adjustment';
    public $product_variant_id = '';
    public $unit_cost = '';
    public $amount = '';
    public $quantity = '';
    public $reason = '';

    protected function rules()
    {
        return [
            'quantity' => 'required|integer|min:1',
            'direction' => 'required|in:add,deduct',
            'reason' => 'required|string|max:255',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'transactionType' => 'required|in:adjustment,sale,loss,purchase',
            'unit_cost' => 'nullable|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
        ];
    }

    public function mount(Product $product, $appSettings, $isBoss)
    {
        $this->product = $product;
        $this->appSettings = $appSettings;
        $this->isBoss = $isBoss;
    }

    public function updatedTransactionType($value)
    {
        if ($value === 'purchase') {
            $this->direction = 'add';
        } elseif (in_array($value, ['sale', 'loss'])) {
            $this->direction = 'deduct';
        }
    }

    public function save()
    {
        $this->validate();

        if (!auth()->user() || !auth()->user()->isBoss()) {
            abort(403, 'Unauthorized Action.');
        }

        DB::transaction(function () {
            $qty = (int) $this->quantity;
            $change = $this->direction === 'add' ? $qty : -$qty;
            $type = $this->transactionType;

            $target = $this->product;
            if (!empty($this->product_variant_id)) {
                $target = \App\Models\ProductVariant::findOrFail($this->product_variant_id);
            }

            $adjustment = $target->adjustStock(
                change: $change,
                type: $type,
                reason: $this->reason,
                userId: auth()->id(),
                unitCost: !empty($this->unit_cost) ? (float) $this->unit_cost : null,
            );

            // Record revenue if it's an external sale
            if ($type === 'sale' && !empty($this->amount)) {
                Revenue::create([
                    'amount' => (float) $this->amount,
                    'recorded_at' => now(),
                    'quote_id' => null,
                    'stock_adjustment_id' => $adjustment->id,
                ]);
            }
        });

        $this->reset(['quantity', 'reason', 'product_variant_id', 'unit_cost', 'amount']);
        $this->direction = 'add';
        $this->transactionType = 'adjustment';

        $this->product->load(['variants', 'stockAdjustments.user', 'stockAdjustments.variant']);

        session()->flash('success', 'Stock updated and financial impact recorded.');
    }

    public function revert($adjustmentId)
    {
        if (!auth()->user() || !auth()->user()->isBoss()) {
            session()->flash('error', 'Unauthorized Action.');
            return;
        }

        $adjustment = StockAdjustment::findOrFail($adjustmentId);

        if ($adjustment->reverted_at) {
            session()->flash('error', 'This adjustment has already been reverted.');
            return;
        }

        DB::transaction(function () use ($adjustment) {
            $target = $adjustment->variant ?: $adjustment->product;

            $adjustment->update(['reverted_at' => now()]);

            $target->adjustStock(
                change: -$adjustment->quantity_change,
                type: 'reversion',
                reason: "Reverted adjustment #{$adjustment->id}: {$adjustment->reason}",
                userId: auth()->id(),
            );

            Revenue::where('stock_adjustment_id', $adjustment->id)
                ->update(['reverted_at' => now()]);
        });

        $this->product->load(['variants', 'stockAdjustments.user', 'stockAdjustments.variant']);
        session()->flash('success', 'Adjustment reverted successfully.');
    }

    public function render()
    {
        return view('livewire.products.stock-panel');
    }
}
