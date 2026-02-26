<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('products.index', [
            'products' => Product::with(['category', 'taxRate'])->latest()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create', [
            'categories' => Category::all(),
            'taxRates' => \App\Models\TaxRate::where('is_active', true)->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'unit_size' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'image' => 'nullable|image|max:10240|mimes:jpeg,jpg,png',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/products', 'public');
            $validated['image_path'] = '/storage/' . $path;
        }

        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'variants', 'taxRate', 'stockAdjustments.user', 'stockAdjustments.quote:id,reference_id']);

        return view('products.show', [
            'product' => $product,
            'isBoss' => auth()->user()->isBoss(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::all(),
            'taxRates' => \App\Models\TaxRate::where('is_active', true)->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'unit_size' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:10240',
        ]);

        unset($validated['stock_quantity']); // stock managed via StockController only

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/products', 'public');
            $validated['image_path'] = '/storage/' . $path;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
