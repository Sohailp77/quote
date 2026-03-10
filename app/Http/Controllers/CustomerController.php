<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Customer::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('company', 'ilike', "%{$search}%")
                  ->orWhere('address', 'ilike', "%{$search}%");
            });
        }

        // Aggregate payment stats from Quotes model by customer name
        // (Since we don't have a formal relationship yet, we'll use name matching for now
        // to stay consistent with the existing codebase logic in 'show' method)
        $customers = $query->latest()->paginate(15)->withQueryString();
        
        foreach ($customers as $customer) {
            $customerQuotes = \App\Models\Quote::where('customer_name', $customer->name)
                ->where('status', 'accepted')
                ->get();
            
            $customer->total_orders = $customerQuotes->count();
            $customer->total_revenue = $customerQuotes->sum('total_amount');
            $customer->pending_balance = $customerQuotes->where('payment_status', '!=', 'paid')->sum('total_amount');
            
            // Determine Health
            if ($customer->total_orders === 0) {
                $customer->payment_health = 'new';
            } elseif ($customer->pending_balance > 0) {
                $customer->payment_health = 'pending';
            } else {
                $customer->payment_health = 'good';
            }
        }

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        \App\Models\Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(\App\Models\Customer $customer)
    {
        // Load quotes and calculate LTV
        $quotes = \App\Models\Quote::where('customer_name', $customer->name)->latest()->get();
        $ltv = $quotes->where('status', 'accepted')->sum('total_amount');

        return view('customers.show', compact('customer', 'quotes', 'ltv'));
    }

    public function edit(\App\Models\Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, \App\Models\Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(\App\Models\Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
