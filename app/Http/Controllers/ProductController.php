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
    public function index(Request $request)
    {
        $query = Product::with(['category', 'taxRate']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'ilike', "%{$search}%")
                  ->orWhere('sku', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
        }

        return view('products.index', [
            'products' => $query->latest()->get()
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
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'opening_stock' => 'nullable|integer|min:0',
            'opening_stock_unit_cost' => 'nullable|numeric|min:0',
            'unit_size' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'image' => 'nullable|image|max:10240|mimes:jpeg,jpg,png',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/products', 'public');
            $validated['image_path'] = '/storage/' . $path;
        }

        // Handle opening stock
        $openingStock = (int) ($validated['opening_stock'] ?? 0);
        $unitCost = isset($validated['opening_stock_unit_cost']) ? (float) $validated['opening_stock_unit_cost'] : null;

        $validated['stock_quantity'] = $openingStock;
        
        // Remove helper fields before create
        unset($validated['opening_stock'], $validated['opening_stock_unit_cost']);

        $product = Product::create($validated);

        // Record initial stock adjustment if > 0
        if ($openingStock > 0) {
            $product->stockAdjustments()->create([
                'user_id' => auth()->id(),
                'quantity_change' => $openingStock,
                'type' => 'initial_stock',
                'reason' => 'Opening stock recorded at product creation.',
                'unit_cost' => $unitCost,
                'stock_after' => $openingStock,
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'variants', 'taxRate', 'stockAdjustments.user', 'stockAdjustments.quote:id,reference_id']);

        $appSettings = \App\Models\CompanySetting::pluck('value', 'key')->all();

        return view('products.show', [
            'product' => $product,
            'isBoss' => auth()->user()->isBoss(),
            'appSettings' => $appSettings,
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
