<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('plan')->withCount('users')->get();
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $plans = \App\Models\Plan::all();
        return view('admin.tenants.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'plan_id' => 'required|exists:plans,id',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8',
        ]);

        $tenant = Tenant::create([
            'company_name' => $validated['company_name'],
            'plan_id' => $validated['plan_id'],
            'is_active' => true,
        ]);

        \App\Models\User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['owner_name'],
            'email' => $validated['owner_email'],
            'password' => bcrypt($validated['owner_password']),
            'role' => 'boss',
            'is_superadmin' => false,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant and Owner created successfully.');
    }

    public function edit(Tenant $tenant)
    {
        $plans = \App\Models\Plan::all();
        return view('admin.tenants.edit', compact('tenant', 'plans'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'plan_id' => 'nullable|exists:plans,id',
            'is_active' => 'boolean',
            'trial_ends_at' => 'nullable|date',
            'subscription_ends_at' => 'nullable|date',
        ]);

        $tenant->update($validated);

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('admin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}
