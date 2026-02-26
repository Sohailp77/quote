<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index', [
            'taxRates' => TaxRate::orderBy('is_active', 'desc')->get(),
            'taxConfiguration' => CompanySetting::getTaxConfiguration(),
            'companyProfile' => CompanySetting::where('group', 'company')->pluck('value', 'key')->all(),
            'businessGoals' => CompanySetting::where('group', 'goals')->pluck('value', 'key')->all(),
            'themeSettings' => CompanySetting::where('group', 'theme')->pluck('value', 'key')->all(),
        ]);
    }

    public function updateTaxConfiguration(Request $request)
    {
        $validated = $request->validate([
            'strategy' => 'required|in:single,split',
            'primary_label' => 'required|string|max:50',
            'secondary_labels' => 'nullable|array',
            'secondary_labels.*' => 'string|max:50',
        ]);

        CompanySetting::updateOrCreate(
            ['group' => 'tax', 'key' => 'configuration'],
            ['value' => $validated]
        );

        return back()->with('success', 'Tax configuration updated successfully.');
    }

    public function storeTaxRate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'is_active' => 'boolean',
        ]);

        TaxRate::create($validated);

        return back()->with('success', 'Tax rate created successfully.');
    }

    public function updateTaxRate(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'is_active' => 'boolean',
        ]);

        $taxRate->update($validated);

        return back()->with('success', 'Tax rate updated successfully.');
    }

    public function destroyTaxRate(TaxRate $taxRate)
    {
        $taxRate->delete();
        return back()->with('success', 'Tax rate deleted successfully.');
    }

    public function updateCompanyProfile(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:50',
            'currency_symbol' => 'nullable|string|max:10',
            // Bank details
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_branch' => 'nullable|string|max:255',
            'bank_qr_code' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('bank_qr_code')) {
            $path = $request->file('bank_qr_code')->store('company', 'public');
            $validated['bank_qr_code'] = $path;
        } else {
            unset($validated['bank_qr_code']);
        }

        foreach ($validated as $key => $value) {
            CompanySetting::updateOrCreate(
                ['group' => 'company', 'key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Company profile updated.');
    }

    public function updateBusinessGoals(Request $request)
    {
        $validated = $request->validate([
            'monthly_revenue_goal' => 'nullable|numeric|min:0',
            'conversion_rate_goal' => 'nullable|numeric|min:0|max:100',
            'monthly_stock_cost_budget' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            CompanySetting::updateOrCreate(
                ['group' => 'goals', 'key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Business goals updated.');
    }

    public function updateThemeAndBrand(Request $request)
    {
        $validated = $request->validate([
            'theme_mode' => 'nullable|string|in:light,dark,system',
            'brand_color_primary' => 'nullable|string', // regex validation can be tricky inside inertia if not done right, we'll keep it simple for now or use regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/
        ]);

        if (!empty($validated['brand_color_primary']) && !preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i', $validated['brand_color_primary'])) {
            return back()->withErrors(['brand_color_primary' => 'Invalid hex color code']);
        }

        foreach ($validated as $key => $value) {
            CompanySetting::updateOrCreate(
                ['group' => 'theme', 'key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Theme and brand settings updated.');
    }
}
