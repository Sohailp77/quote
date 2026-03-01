<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $categories = $query->latest()->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_name' => 'required|string|max:50',
            'metric_type' => 'required|in:area,weight,fixed',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048|mimes:jpeg,jpg,png', // 2MB Max
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/categories', 'public');
            $validated['image_path'] = '/storage/' . $path;
        }

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', [
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_name' => 'required|string|max:50',
            'metric_type' => 'required|in:area,weight,fixed',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048|mimes:jpeg,jpg,png', // 2MB Max
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/categories', 'public');
            $validated['image_path'] = '/storage/' . $path;
        }

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Simple check before delete - optionally check for products
        if ($category->products()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category with associated products.']);
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
