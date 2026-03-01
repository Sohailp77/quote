<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class VariantManager extends Component
{
    use WithFileUploads;

    public Product $product;
    public $appSettings;

    public $showForm = false;
    public $editingVariantId = null;

    public $name = '';
    public $sku = '';
    public $stock_quantity = '';
    public $variant_price = '';
    public $image;

    // For rendering validation errors
    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:product_variants,sku,' . ($this->editingVariantId ?? 'NULL'),
            'variant_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:10240',
        ];

        if (!$this->editingVariantId) {
            $rules['stock_quantity'] = 'required|integer|min:0';
        }

        return $rules;
    }

    public function mount(Product $product, $appSettings)
    {
        $this->product = $product;
        $this->appSettings = $appSettings;
    }

    public function toggleForm()
    {
        if ($this->showForm) {
            $this->cancelEdit();
        } else {
            $this->showForm = true;
        }
    }

    public function editVariant($id)
    {
        $variant = ProductVariant::findOrFail($id);

        $this->editingVariantId = $variant->id;
        $this->name = $variant->name;
        $this->sku = $variant->sku;
        // stock_quantity is disabled during edit, so we don't need to populate it for editing purposes
        $this->stock_quantity = $variant->stock_quantity;
        $this->variant_price = $variant->variant_price;
        $this->image = null; // Clear any existing file upload state

        $this->showForm = true;
    }

    public function cancelEdit()
    {
        $this->editingVariantId = null;
        $this->reset(['name', 'sku', 'stock_quantity', 'variant_price', 'image']);
        $this->showForm = false;
        $this->resetValidation();
    }

    public function save()
    {
        $validated = $this->validate();

        $data = [
            'product_id' => $this->product->id,
            'name' => $this->name,
            'sku' => empty($this->sku) ? null : $this->sku,
            'variant_price' => empty($this->variant_price) ? null : $this->variant_price,
        ];

        if (!$this->editingVariantId) {
            $data['stock_quantity'] = (int) $this->stock_quantity;
        }

        if ($this->image) {
            $path = $this->image->store('uploads/variants', 'public');
            $data['image_path'] = '/storage/' . $path;
        }

        if ($this->editingVariantId) {
            $variant = ProductVariant::findOrFail($this->editingVariantId);
            $variant->update($data);
            session()->flash('success', 'Variant updated successfully.');
        } else {
            ProductVariant::create($data);
            session()->flash('success', 'Variant added successfully.');
        }

        // Refresh product variants relationship
        $this->product->load('variants');
        $this->cancelEdit();
    }

    public function deleteVariant($id)
    {
        $variant = ProductVariant::findOrFail($id);

        // Optionally handle image deletion
        if ($variant->image_path && Storage::disk('public')->exists(str_replace('/storage/', '', $variant->image_path))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $variant->image_path));
        }

        $variant->delete();

        $this->product->load('variants');
        session()->flash('success', 'Variant deleted successfully.');
    }

    public function render()
    {
        return view('livewire.products.variant-manager');
    }
}
