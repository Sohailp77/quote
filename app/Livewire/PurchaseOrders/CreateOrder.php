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
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['selectedProductString', 'product_id', 'product_variant_id', 'quantity', 'unit_cost', 'estimated_arrival']);
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
