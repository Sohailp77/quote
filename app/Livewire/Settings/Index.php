<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CompanySetting;
use App\Models\TaxRate;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads;

    public $activeTab = 'general';

    // General & Bank
    public $company_name, $company_email, $company_phone, $gstin, $currency_symbol, $company_address;
    public $bank_name, $bank_account_name, $bank_account_number, $bank_ifsc, $bank_branch;
    public $bank_qr_code; // For new upload
    public $existing_bank_qr_code;

    // Theme
    public $theme_mode = 'system', $brand_color_primary = '#6366f1';

    // Tax Config
    public $tax_strategy = 'single', $tax_primary_label = 'Tax';
    public $tax_secondary_labels = ['CGST', 'SGST'];

    // Tax Rates
    public $taxRates;
    public $showRateModal = false;
    public $editingRateId = null;
    public $rate_name = '', $rate_value = '', $rate_is_active = true;

    // Goals
    public $monthly_revenue_goal = '', $conversion_rate_goal = '', $monthly_stock_cost_budget = '';

    // Danger Zone
    public $showResetModal = false;
    public $resetConfirm = '';

    public function mount()
    {
        $this->activeTab = session('active_tab', 'general');
        $this->loadProfile();
        $this->loadTheme();
        $this->loadTaxConfig();
        $this->loadGoals();
        $this->taxRates = TaxRate::orderBy('is_active', 'desc')->get();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        session()->put('active_tab', $tab);
        $this->dispatch('init-lucide');
    }

    public function loadProfile()
    {
        $profile = CompanySetting::where('group', 'company')->pluck('value', 'key')->all();
        $this->company_name = $profile['company_name'] ?? '';
        $this->company_email = $profile['company_email'] ?? '';
        $this->company_phone = $profile['company_phone'] ?? '';
        $this->gstin = $profile['gstin'] ?? '';
        $this->currency_symbol = $profile['currency_symbol'] ?? '₹';
        $this->company_address = $profile['company_address'] ?? '';

        $this->bank_name = $profile['bank_name'] ?? '';
        $this->bank_account_name = $profile['bank_account_name'] ?? '';
        $this->bank_account_number = $profile['bank_account_number'] ?? '';
        $this->bank_ifsc = $profile['bank_ifsc'] ?? '';
        $this->bank_branch = $profile['bank_branch'] ?? '';
        $this->existing_bank_qr_code = $profile['bank_qr_code'] ?? null;
    }

    public function loadTheme()
    {
        $theme = CompanySetting::where('group', 'theme')->pluck('value', 'key')->all();
        $this->theme_mode = $theme['theme_mode'] ?? 'system';
        $this->brand_color_primary = $theme['brand_color_primary'] ?? '#6366f1';
    }

    public function loadTaxConfig()
    {
        $config = CompanySetting::getTaxConfiguration();
        $this->tax_strategy = $config['strategy'] ?? 'single';
        $this->tax_primary_label = $config['primary_label'] ?? 'Tax';
        $this->tax_secondary_labels = $config['secondary_labels'] ?? ['CGST', 'SGST'];
    }

    public function loadGoals()
    {
        $goals = CompanySetting::where('group', 'goals')->pluck('value', 'key')->all();
        $this->monthly_revenue_goal = $goals['monthly_revenue_goal'] ?? '';
        $this->conversion_rate_goal = $goals['conversion_rate_goal'] ?? '';
        $this->monthly_stock_cost_budget = $goals['monthly_stock_cost_budget'] ?? '';
    }

    public function saveGeneral()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'gstin' => 'nullable|string|max:50',
            'currency_symbol' => 'nullable|string|max:10',
            'company_address' => 'nullable|string',
        ]);

        $data = [
            'company_name' => $this->company_name,
            'company_email' => $this->company_email,
            'company_phone' => $this->company_phone,
            'gstin' => $this->gstin,
            'currency_symbol' => $this->currency_symbol,
            'company_address' => $this->company_address,
        ];

        foreach ($data as $key => $value) {
            CompanySetting::updateOrCreate(['group' => 'company', 'key' => $key], ['value' => $value]);
        }

        session()->flash('success', 'Profile updated effectively.');
    }

    public function saveBank()
    {
        $this->validate([
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_branch' => 'nullable|string|max:255',
            'bank_qr_code' => 'nullable|image|max:2048',
        ]);

        if ($this->bank_qr_code) {
            $path = $this->bank_qr_code->store('company', 'public');
            CompanySetting::updateOrCreate(['group' => 'company', 'key' => 'bank_qr_code'], ['value' => $path]);
            $this->existing_bank_qr_code = $path;
        }

        $data = [
            'bank_name' => $this->bank_name,
            'bank_account_name' => $this->bank_account_name,
            'bank_account_number' => $this->bank_account_number,
            'bank_ifsc' => $this->bank_ifsc,
            'bank_branch' => $this->bank_branch,
        ];

        foreach ($data as $key => $value) {
            CompanySetting::updateOrCreate(['group' => 'company', 'key' => $key], ['value' => $value]);
        }

        session()->flash('success', 'Bank details updated.');
    }

    public function saveTheme()
    {
        $this->validate([
            'theme_mode' => 'required|in:light,dark,system',
            'brand_color_primary' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i'],
        ]);

        CompanySetting::updateOrCreate(['group' => 'theme', 'key' => 'theme_mode'], ['value' => $this->theme_mode]);
        CompanySetting::updateOrCreate(['group' => 'theme', 'key' => 'brand_color_primary'], ['value' => $this->brand_color_primary]);

        $this->dispatch('theme-updated', ['mode' => $this->theme_mode, 'color' => $this->brand_color_primary]);
        session()->flash('success', 'Appearance updated. Refresh to apply to everything.');
    }

    public function saveTaxConfig()
    {
        $this->validate([
            'tax_strategy' => 'required|in:single,split',
            'tax_primary_label' => 'required|string|max:50',
            'tax_secondary_labels' => 'nullable|array',
            'tax_secondary_labels.*' => 'string|max:50',
        ]);

        $value = [
            'strategy' => $this->tax_strategy,
            'primary_label' => $this->tax_primary_label,
            'secondary_labels' => $this->tax_secondary_labels,
        ];

        CompanySetting::updateOrCreate(['group' => 'tax', 'key' => 'configuration'], ['value' => $value]);
        session()->flash('success', 'Tax Strategy configured.');
    }

    public function openRateModal($id = null)
    {
        if ($id) {
            $rate = TaxRate::find($id);
            $this->editingRateId = $id;
            $this->rate_name = $rate->name;
            $this->rate_value = $rate->rate;
            $this->rate_is_active = $rate->is_active;
        } else {
            $this->editingRateId = null;
            $this->rate_name = '';
            $this->rate_value = '';
            $this->rate_is_active = true;
        }
        $this->showRateModal = true;
        $this->dispatch('init-lucide');
    }

    public function saveRate()
    {
        $this->validate([
            'rate_name' => 'required|string|max:255',
            'rate_value' => 'required|numeric|min:0',
        ]);

        if ($this->editingRateId) {
            TaxRate::find($this->editingRateId)->update([
                'name' => $this->rate_name,
                'rate' => $this->rate_value,
                'is_active' => $this->rate_is_active,
                'type' => 'percentage'
            ]);
            session()->flash('success', 'Tax rate updated.');
        } else {
            TaxRate::create([
                'name' => $this->rate_name,
                'rate' => $this->rate_value,
                'is_active' => $this->rate_is_active,
                'type' => 'percentage'
            ]);
            session()->flash('success', 'Tax rate added.');
        }

        $this->showRateModal = false;
        $this->taxRates = TaxRate::orderBy('is_active', 'desc')->get();
    }

    public function deleteRate($id)
    {
        TaxRate::find($id)->delete();
        $this->taxRates = TaxRate::orderBy('is_active', 'desc')->get();
        session()->flash('success', 'Tax rate removed.');
    }

    public function saveGoals()
    {
        $this->validate([
            'monthly_revenue_goal' => 'nullable|numeric|min:0',
            'conversion_rate_goal' => 'nullable|numeric|min:0|max:100',
            'monthly_stock_cost_budget' => 'nullable|numeric|min:0',
        ]);

        $data = [
            'monthly_revenue_goal' => $this->monthly_revenue_goal,
            'conversion_rate_goal' => $this->conversion_rate_goal,
            'monthly_stock_cost_budget' => $this->monthly_stock_cost_budget,
        ];

        foreach ($data as $key => $value) {
            CompanySetting::updateOrCreate(['group' => 'goals', 'key' => $key], ['value' => $value]);
        }
        session()->flash('success', 'Business goals saved.');
    }

    public function startFresh()
    {
        if ($this->resetConfirm !== 'DELETE') {
            return;
        }

        DB::transaction(function () {
            QuoteItem::truncate();
            Quote::query()->delete();
            DB::table('revenues')->truncate();
            DB::table('stock_adjustments')->truncate();
            PurchaseOrder::query()->delete();
            DB::table('products')->update(['stock_quantity' => 0]);
            DB::table('product_variants')->update(['stock_quantity' => 0]);
        });

        $this->showResetModal = false;
        $this->resetConfirm = '';
        session()->flash('success', 'All financial logs and stats reset successfully.');
    }

    public function render()
    {
        return view('livewire.settings.index');
    }
}
