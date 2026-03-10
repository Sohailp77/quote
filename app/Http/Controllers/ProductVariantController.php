<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'sku' => [
                'nullable',
                'string',
                \Illuminate\Validation\Rule::unique('product_variants', 'sku')->where('tenant_id', auth()->user()->tenant_id),
            ],
            'image_path' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'variant_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/variants', 'public');
            $validated['image_path'] = '/storage/' . $path;
        }

        ProductVariant::create($validated);

        return back()->with('success', 'Variant added successfully.');
    }

    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();
        return back()->with('success', 'Variant deleted successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductVariant $productVariant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => [
                'nullable',
                'string',
                \Illuminate\Validation\Rule::unique('product_variants', 'sku')->where('tenant_id', auth()->user()->tenant_id)->ignore($productVariant->id),
            ],
            'image_path' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'variant_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:10240|mimes:jpeg,jpg,png',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/variants', 'public');
            $validated['image_path'] = '/storage/' . $path;
        }

        //show error if sku is not unique
        if ($request->has('sku')) {
            $sku = $request->sku;
            $productVariant->sku = $sku;
            $productVariant->save();
        }

        $productVariant->update($validated);

        return back()->with('success', 'Variant updated successfully.');
    }
}
