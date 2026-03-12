<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class CreateOrder extends Component
{
    public $products;
    public $appSettings;

    public $isModalOpen = false;

    public $selectedProductString = '';
    public $product_id = '';
    public $product_variant_id = '';

    public $quantity = '';
    public $unit_cost = '';
    public $update_cost_price = true;
    public $estimated_arrival = '';

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'product_variant_id' => 'nullable|exists:product_variants,id',
        'quantity' => 'required|integer|min:1',
        'unit_cost' => 'required|numeric|min:0',
        'estimated_arrival' => 'nullable|date',
    ];

    public function mount($products, $appSettings)
    {
        $this->products = $products;
        $this->appSettings = $appSettings;
    }

    public function updatedSelectedProductString($value)
    {
        if (empty($value)) {
            $this->product_id = null;
            $this->product_variant_id = null;
            return;
        }

        $parsed = json_decode($value, true);
        $this->product_id = $parsed['product_id'] ?? null;
        $this->product_variant_id = $parsed['variant_id'] ?? null;
        
        // Auto-fill cost price
        $this->unit_cost = '';
        if ($this->product_variant_id) {
            $variant = \App\Models\ProductVariant::find($this->product_variant_id);
            if ($variant) {
                $this->unit_cost = $variant->cost_price ?? '';
            }
        } elseif ($this->product_id) {
            $product = \App\Models\Product::find($this->product_id);
            if ($product) {
                $this->unit_cost = $product->cost_price ?? '';
            }
        }
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['selectedProductString', 'product_id', 'product_variant_id', 'quantity', 'unit_cost', 'estimated_arrival']);
        $this->update_cost_price = true;
        $this->resetValidation();
    }

    public function save()
    {
        // Sanitize empty strings to null for nullable fields to pass validation
        if ($this->product_variant_id === '') {
            $this->product_variant_id = null;
        }
        if ($this->estimated_arrival === '') {
            $this->estimated_arrival = null;
        }
        if (!auth()->user() || !auth()->user()->isBoss()) {
            abort(403, 'Unauthorized Action.');
        }

        $validated = $this->validate();

        PurchaseOrder::create([
            'product_id' => $validated['product_id'],
            'product_variant_id' => (isset($validated['product_variant_id']) && $validated['product_variant_id'] !== '') ? $validated['product_variant_id'] : null,
            'quantity' => $validated['quantity'],
            'unit_cost' => $validated['unit_cost'],
            'estimated_arrival' => $validated['estimated_arrival'] ?? null,
            'status' => 'pending',
        ]);

        if ($this->update_cost_price) {
            if ($validated['product_variant_id'] && $validated['product_variant_id'] !== '') {
                \App\Models\ProductVariant::where('id', $validated['product_variant_id'])->update([
                    'cost_price' => $validated['unit_cost']
                ]);
            } else {
                \App\Models\Product::where('id', $validated['product_id'])->update([
                    'cost_price' => $validated['unit_cost']
                ]);
            }
        }

        $this->closeModal();
        session()->flash('success', 'Purchase order created successfully.');
        // Redirect to reload page so the main table updates, or emit event
        return redirect()->route('purchase-orders.index');
    }

    public function render()
    {
        return view('livewire.purchase-orders.create-order');
    }
}
