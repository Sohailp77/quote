<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanySetting;
use App\Models\TaxRate;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = session('active_tab', 'general');

        // Profile
        $profile = CompanySetting::where('group', 'company')->pluck('value', 'key')->all();
        // Theme
        $theme = CompanySetting::where('group', 'theme')->pluck('value', 'key')->all();
        // Tax Config
        $taxConfig = CompanySetting::getTaxConfiguration();
        // Goals
        $goals = CompanySetting::where('group', 'goals')->pluck('value', 'key')->all();
        // Tax Rates
        $taxRates = TaxRate::orderBy('is_active', 'desc')->get();
        // Quote Defaults
        $quoteDefaults = CompanySetting::where('group', 'quote_defaults')->pluck('value', 'key')->all();

        return view('settings.index', compact(
            'activeTab',
            'profile',
            'theme',
            'taxConfig',
            'goals',
            'taxRates',
            'quoteDefaults'
        ));
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'gstin' => 'nullable|string|max:50',
            'currency_symbol' => 'nullable|string|max:10',
            'company_address' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            CompanySetting::updateOrCreate(['group' => 'company', 'key' => $key], ['value' => $value]);
        }

        return redirect()->route('settings.index')->with(['active_tab' => 'general', 'success' => 'Profile updated successfully.']);
    }

    public function updateBank(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_branch' => 'nullable|string|max:255',
            'bank_qr_code' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('bank_qr_code')) {
            $path = $request->file('bank_qr_code')->store('company', 'public');
            CompanySetting::updateOrCreate(['group' => 'company', 'key' => 'bank_qr_code'], ['value' => $path]);
        }

        $data = collect($validated)->except('bank_qr_code')->toArray();
        foreach ($data as $key => $value) {
            CompanySetting::updateOrCreate(['group' => 'company', 'key' => $key], ['value' => $value]);
        }

        return redirect()->route('settings.index')->with(['active_tab' => 'bank', 'success' => 'Bank details updated.']);
    }

    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'theme_mode' => 'required|in:light,dark,system',
            'brand_color_primary' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i'],
        ]);

        CompanySetting::updateOrCreate(['group' => 'theme', 'key' => 'theme_mode'], ['value' => $validated['theme_mode']]);
        CompanySetting::updateOrCreate(['group' => 'theme', 'key' => 'brand_color_primary'], ['value' => $validated['brand_color_primary']]);

        return redirect()->route('settings.index')->with(['active_tab' => 'theme', 'success' => 'Appearance updated.']);
    }

    public function updateTaxConfig(Request $request)
    {
        $validated = $request->validate([
            'tax_strategy' => 'required|in:single,split',
            'tax_primary_label' => 'required|string|max:50',
            'tax_secondary_labels' => 'nullable|array',
            'tax_secondary_labels.*' => 'string|max:50',
        ]);

        $value = [
            'strategy' => $validated['tax_strategy'],
            'primary_label' => $validated['tax_primary_label'],
            'secondary_labels' => $validated['tax_secondary_labels'] ?? ['CGST', 'SGST'],
        ];

        CompanySetting::updateOrCreate(['group' => 'tax', 'key' => 'configuration'], ['value' => $value]);

        return redirect()->route('settings.index')->with(['active_tab' => 'tax_config', 'success' => 'Tax Strategy configured.']);
    }

    public function storeTaxRate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['type'] = 'percentage';

        TaxRate::create($validated);

        return redirect()->route('settings.index')->with(['active_tab' => 'tax_rates', 'success' => 'Tax rate added.']);
    }

    public function updateTaxRate(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $taxRate->update($validated);

        return redirect()->route('settings.index')->with(['active_tab' => 'tax_rates', 'success' => 'Tax rate updated.']);
    }

    public function destroyTaxRate(TaxRate $taxRate)
    {
        $taxRate->delete();
        return redirect()->route('settings.index')->with(['active_tab' => 'tax_rates', 'success' => 'Tax rate removed.']);
    }

    public function updateGoals(Request $request)
    {
        $validated = $request->validate([
            'monthly_revenue_goal' => 'nullable|numeric|min:0',
            'conversion_rate_goal' => 'nullable|numeric|min:0|max:100',
            'monthly_stock_cost_budget' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            CompanySetting::updateOrCreate(['group' => 'goals', 'key' => $key], ['value' => $value]);
        }

        return redirect()->route('settings.index')->with(['active_tab' => 'goals', 'success' => 'Business goals saved.']);
    }

    public function updateQuoteDefaults(Request $request)
    {
        $validated = $request->validate([
            'default_notes' => 'nullable|string',
            'default_terms' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            CompanySetting::updateOrCreate(['group' => 'quote_defaults', 'key' => $key], ['value' => $value]);
        }

        return redirect()->route('settings.index')->with(['active_tab' => 'quote_defaults', 'success' => 'Quote defaults saved.']);
    }
}
