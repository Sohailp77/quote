<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-6 lg:py-8" x-data="{ 
    activeTab: '{{ $activeTab }}', 
    showRateModal: false, 
    editingRateId: null, 
    rate_name: '', 
    rate_value: '', 
    rate_is_active: true, 
    showResetModal: false, 
    resetConfirm: '',
    openRateModal(id = null, name = '', rate = '', active = true) {
        this.editingRateId = id;
        this.rate_name = name;
        this.rate_value = rate;
        this.rate_is_active = active;
        this.showRateModal = true;
        $nextTick(() => { window.lucide?.createIcons(); });
    }
}">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show"
                    class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl relative flex justify-between items-center shadow-sm transition-all duration-300">
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
            @endif
            @if(session('error') || $errors->any())
                <div x-data="{ show: true }" x-show="show"
                    class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl relative flex justify-between items-center shadow-sm transition-all duration-300">
                    <span class="text-sm font-semibold">{{ session('error') ?? 'Please correct the errors below.' }}</span>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-6">
                <!-- Sidebar Navigation -->
                <div class="w-full md:w-56 flex-shrink-0">
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-3 space-y-1">
                        @php
                            $navItems = [
                                ['id' => 'general', 'label' => 'General Profile', 'icon' => 'building-2'],
                                ['id' => 'bank', 'label' => 'Bank Details', 'icon' => 'landmark'],
                                ['id' => 'theme', 'label' => 'Brand & Appearance', 'icon' => 'palette'],
                                ['id' => 'tax_config', 'label' => 'Tax Configuration', 'icon' => 'settings'],
                                ['id' => 'tax_rates', 'label' => 'Tax Rates', 'icon' => 'percent'],
                                ['id' => 'goals', 'label' => 'Business Goals', 'icon' => 'target'],
                                ['id' => 'danger', 'label' => 'Danger Zone', 'icon' => 'alert-triangle', 'danger' => true],
                            ];
                        @endphp

                        @foreach($navItems as $item)
                            <button @click="activeTab = '{{ $item['id'] }}'; $nextTick(() => window.lucide?.createIcons());"
                                class="flex items-center w-full px-4 py-3 text-sm font-semibold rounded-2xl transition-all"
                                :class="activeTab === '{{ $item['id'] }}' ? '{{ isset($item['danger']) && $item['danger'] ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-900 dark:bg-brand-500 text-white shadow-sm' }}' : '{{ isset($item['danger']) && $item['danger'] ? 'text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-200' }}'">
                                <x-dynamic-component :component="'lucide-' . ($item['icon'])" class="w-4 h-4 mr-3" />
                                {{ $item['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Content Area -->
                <div class="flex-1">
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6 min-h-[500px]">

                        <!-- GENERAL TAB -->
                        <div x-show="activeTab === 'general'" x-cloak>
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                Company Profile</h3>
                            <form action="{{ route('settings.general.update') }}" method="POST"
                                class="space-y-6 max-w-xl">
                                @csrf
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="company_name">Company Name</label>
                                    <input type="text" name="company_name" id="company_name" required
                                        value="{{ old('company_name', $profile['company_name'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                    @error('company_name')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="company_email">Official Email</label>
                                    <input type="email" name="company_email" id="company_email"
                                        value="{{ old('company_email', $profile['company_email'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="company_phone">Phone Number</label>
                                    <input type="text" name="company_phone" id="company_phone"
                                        value="{{ old('company_phone', $profile['company_phone'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="gstin">GSTIN / Tax ID</label>
                                    <input type="text" name="gstin" id="gstin"
                                        value="{{ old('gstin', $profile['gstin'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="currency_symbol">Currency Symbol</label>
                                    <select name="currency_symbol" id="currency_symbol"
                                        class="mt-1 block w-full max-w-[120px] border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                        @php $currentCurrency = old('currency_symbol', $profile['currency_symbol'] ?? '₹'); @endphp
                                        @foreach(['$' => '$ (USD)', '€' => '€ (EUR)', '£' => '£ (GBP)', '₹' => '₹ (INR)', '﷼' => '﷼ (SAR)', 'د.إ' => 'د.إ (AED)'] as $sym => $label)
                                            <option value="{{ $sym }}" {{ $currentCurrency === $sym ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Used on invoices,
                                        dashboard, and quotes.</p>
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="company_address">Address</label>
                                    <textarea name="company_address" id="company_address" rows="3"
                                        class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 dark:text-slate-300 focus:border-slate-400 focus:ring-slate-200 rounded-xl shadow-sm">{{ old('company_address', $profile['company_address'] ?? '') }}</textarea>
                                </div>
                                <div
                                    class="flex items-center gap-4 border-t border-slate-100 dark:border-slate-800 pt-4 mt-6">
                                    <button type="submit" x-data="{ submitting: false }"
                                        @submit.window="submitting = true" :class="{ 'opacity-50': submitting }"
                                        class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition">
                                        Save Profile
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- BANK DETAILS TAB -->
                        <div x-show="activeTab === 'bank'" x-cloak>
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                Bank Details</h3>
                            <p class="text-sm text-slate-400 dark:text-slate-500 mb-6">These details appear on the
                                bottom of every PDF quotation.</p>
                            <form action="{{ route('settings.bank.update') }}" method="POST"
                                enctype="multipart/form-data" class="space-y-6 max-w-xl">
                                @csrf
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_name">Bank Name</label>
                                    <input type="text" name="bank_name" id="bank_name" placeholder="e.g. HDFC Bank"
                                        value="{{ old('bank_name', $profile['bank_name'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_account_name">Account Name</label>
                                    <input type="text" name="bank_account_name" id="bank_account_name"
                                        placeholder="As per bank records"
                                        value="{{ old('bank_account_name', $profile['bank_account_name'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_account_number">Account Number</label>
                                    <input type="text" name="bank_account_number" id="bank_account_number"
                                        placeholder="e.g. 50200012345678"
                                        value="{{ old('bank_account_number', $profile['bank_account_number'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_ifsc">IFSC / SWIFT Code</label>
                                    <input type="text" name="bank_ifsc" id="bank_ifsc" placeholder="e.g. HDFC0001234"
                                        value="{{ old('bank_ifsc', $profile['bank_ifsc'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_branch">Branch</label>
                                    <input type="text" name="bank_branch" id="bank_branch"
                                        placeholder="e.g. Main Branch, New Delhi"
                                        value="{{ old('bank_branch', $profile['bank_branch'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_qr_code">Payment QR Code Image (Optional)</label>
                                    <input type="file" name="bank_qr_code" id="bank_qr_code" accept="image/*"
                                        class="mt-1 block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                                    @if(isset($profile['bank_qr_code']) && $profile['bank_qr_code'])
                                        <div class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">
                                            A QR code is currently uploaded. <a
                                                href="{{ asset('storage/' . $profile['bank_qr_code']) }}" target="_blank"
                                                class="underline">View QR Code</a>
                                        </div>
                                    @endif
                                    @error('bank_qr_code')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div
                                    class="flex items-center gap-4 border-t border-slate-100 dark:border-slate-800 pt-4 mt-6">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition">
                                        Save Bank Details
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- THEME TAB -->
                        <div x-show="activeTab === 'theme'" x-cloak
                            x-data="{ brandColor: '{{ old('brand_color_primary', $theme['brand_color_primary'] ?? '#6366f1') }}' }">
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                Brand & Appearance</h3>
                            <p class="text-sm text-slate-400 dark:text-slate-500 mb-6">Customize the look and feel of
                                your application and PDF quotes.</p>
                            <form action="{{ route('settings.theme.update') }}" method="POST"
                                class="space-y-6 max-w-xl">
                                @csrf
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300">Theme
                                        Mode</label>
                                    <div class="mt-2 flex gap-4">
                                        @php $currentThemeMode = old('theme_mode', $theme['theme_mode'] ?? 'system'); @endphp
                                        @foreach(['light', 'dark', 'system'] as $m)
                                            <div class="flex items-center">
                                                <input type="radio" id="theme_{{ $m }}" name="theme_mode" value="{{ $m }}"
                                                    {{ $currentThemeMode === $m ? 'checked' : '' }}
                                                    class="focus:ring-brand-500 h-4 w-4 text-brand-600 dark:text-brand-400 border-slate-300 dark:border-slate-600 dark:bg-slate-900">
                                                <label for="theme_{{ $m }}"
                                                    class="ml-2 block text-sm font-medium text-slate-700 dark:text-slate-300 capitalize">{{ $m }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="brand_color_primary">Primary Brand Color</label>
                                    <div class="flex items-center gap-3 mt-2">
                                        <input type="color" x-model="brandColor" id="brand_color_picker"
                                            class="h-10 w-10 border-0 p-0 rounded-lg cursor-pointer flex-shrink-0">
                                        <input type="text" name="brand_color_primary" x-model="brandColor"
                                            id="brand_color_primary"
                                            class="w-32 uppercase font-mono text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                            placeholder="#6366F1" pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
                                    </div>

                                    <div class="mt-4">
                                        <label
                                            class="block font-medium text-sm text-slate-700 dark:text-slate-300 mb-3">Predefined
                                            Combos</label>
                                        <div class="flex gap-3">
                                            @php
                                                $colors = [
                                                    ['name' => 'Default Indigo', 'hex' => '#6366f1'],
                                                    ['name' => 'Calm Emerald', 'hex' => '#10b981'],
                                                    ['name' => 'Vibrant Rose', 'hex' => '#f43f5e'],
                                                    ['name' => 'Warm Amber', 'hex' => '#f59e0b'],
                                                    ['name' => 'Ocean Sky', 'hex' => '#0ea5e9'],
                                                    ['name' => 'Professional Slate', 'hex' => '#475569']
                                                ];
                                            @endphp
                                            @foreach($colors as $c)
                                                <button type="button" @click="brandColor = '{{ strtolower($c['hex']) }}'"
                                                    class="w-8 h-8 rounded-full border-2 transition-all shadow-sm border-transparent hover:scale-110"
                                                    :class="{ 'border-slate-900 dark:border-white scale-110 ring-2 ring-slate-400 ring-offset-2 dark:ring-offset-slate-900': brandColor.toLowerCase() === '{{ strtolower($c['hex']) }}' }"
                                                    style="background-color: {{ $c['hex'] }}"
                                                    title="{{ $c['name'] }}"></button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @error('brand_color_primary')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div
                                    class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition">
                                        Save Theme Attributes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- TAX CONFIGURATION TAB -->
                        <div x-show="activeTab === 'tax_config'" x-cloak
                            x-data="{ strategy: '{{ old('tax_strategy', $taxConfig['strategy'] ?? 'single') }}' }">
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                Tax Configuration</h3>
                            <form action="{{ route('settings.tax-config.update') }}" method="POST"
                                class="space-y-6 max-w-xl">
                                @csrf
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300">Tax
                                        Strategy</label>
                                    <div class="mt-2 space-y-4">
                                        <div class="flex items-center">
                                            <input type="radio" id="strategy_single" name="tax_strategy" value="single"
                                                x-model="strategy"
                                                class="focus:ring-slate-500 h-4 w-4 text-slate-900 dark:text-white border-slate-300 dark:border-slate-600 dark:bg-slate-900">
                                            <label for="strategy_single"
                                                class="ml-3 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                                Single Rate (Standard)
                                                <p class="text-slate-500 dark:text-slate-400 text-xs font-normal">Apply
                                                    a single tax percentage (e.g. VAT 10%)</p>
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="radio" id="strategy_split" name="tax_strategy" value="split"
                                                x-model="strategy"
                                                class="focus:ring-slate-500 h-4 w-4 text-slate-900 dark:text-white border-slate-300 dark:border-slate-600 dark:bg-slate-900">
                                            <label for="strategy_split"
                                                class="ml-3 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                                Split Tax (Dual GST/HST)
                                                <p class="text-slate-500 dark:text-slate-400 text-xs font-normal">Split
                                                    tax into components (e.g. CGST + SGST)</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="tax_primary_label">Primary Tax Label</label>
                                    <input type="text" name="tax_primary_label" id="tax_primary_label"
                                        placeholder="e.g. GST"
                                        value="{{ old('tax_primary_label', $taxConfig['primary_label'] ?? 'Tax') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>

                                <div x-show="strategy === 'split'" x-cloak
                                    class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Split
                                        Components Labels</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <input type="text" name="tax_secondary_labels[0]"
                                            placeholder="Component 1 (e.g. CGST)"
                                            value="{{ old('tax_secondary_labels.0', $taxConfig['secondary_labels'][0] ?? 'CGST') }}"
                                            class="block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                        <input type="text" name="tax_secondary_labels[1]"
                                            placeholder="Component 2 (e.g. SGST)"
                                            value="{{ old('tax_secondary_labels.1', $taxConfig['secondary_labels'][1] ?? 'SGST') }}"
                                            class="block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Currently supports 2-way
                                        split (50/50) only.</p>
                                </div>

                                <div
                                    class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800 mt-6">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition">
                                        Update Configuration
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- TAX RATES TAB -->
                        <div x-show="activeTab === 'tax_rates'" x-cloak>
                            <div
                                class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Tax Rates Table</h3>
                                <button @click="openRateModal()"
                                    class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition">
                                    <x-lucide-plus class="w-4 h-4 mr-2" /> Add New Rate
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                    <thead class="bg-slate-50 dark:bg-slate-800">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                Rate (%)</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-slate-900 divide-y divide-slate-100 dark:divide-slate-800">
                                        @forelse($taxRates as $rate)
                                            <tr>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                                    {{ $rate->name }}</td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                                    {{ $rate->rate }}%</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rate->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-400' }}">
                                                        {{ $rate->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end gap-2">
                                                    <button
                                                        @click="openRateModal({{ $rate->id }}, '{{ addslashes($rate->name) }}', '{{ $rate->rate }}', {{ $rate->is_active ? 'true' : 'false' }})"
                                                        class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all">
                                                        <x-lucide-edit-2 class="w-4 h-4" />
                                                    </button>
                                                    <form action="{{ route('settings.tax-rates.destroy', $rate) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this tax rate?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-1.5 text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all">
                                                            <x-lucide-trash-2 class="w-4 h-4" />
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4"
                                                    class="px-6 py-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                                    No tax rates found. Create one to get started.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- BUSINESS GOALS TAB -->
                        <div x-show="activeTab === 'goals'" x-cloak>
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                Business Goals & Targets</h3>
                            <p class="text-sm text-slate-400 dark:text-slate-500 mb-6">Set performance targets to track
                                your progress on the analytics dashboard.</p>
                            <form action="{{ route('settings.goals.update') }}" method="POST"
                                class="space-y-6 max-w-xl">
                                @csrf
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="monthly_revenue_goal">Monthly Revenue Goal
                                        ({{ $profile['currency_symbol'] ?? '₹' }})</label>
                                    <input type="number" name="monthly_revenue_goal" id="monthly_revenue_goal"
                                        placeholder="e.g. 500000"
                                        value="{{ old('monthly_revenue_goal', $goals['monthly_revenue_goal'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="conversion_rate_goal">Conversion Rate Target (%)</label>
                                    <input type="number" step="0.1" name="conversion_rate_goal"
                                        id="conversion_rate_goal" placeholder="e.g. 25"
                                        value="{{ old('conversion_rate_goal', $goals['conversion_rate_goal'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="monthly_stock_cost_budget">Monthly Stock Budget
                                        ({{ $profile['currency_symbol'] ?? '₹' }})</label>
                                    <input type="number" name="monthly_stock_cost_budget" id="monthly_stock_cost_budget"
                                        placeholder="e.g. 200000"
                                        value="{{ old('monthly_stock_cost_budget', $goals['monthly_stock_cost_budget'] ?? '') }}"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div
                                    class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800 mt-6">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition">
                                        Save Goals
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- DANGER ZONE TAB -->
                        <div x-show="activeTab === 'danger'" x-cloak>
                            <h3
                                class="text-xl font-bold text-red-600 dark:text-red-400 border-b border-red-100 dark:border-red-800 pb-4 mb-6">
                                Danger Zone</h3>
                            <div
                                class="p-6 bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-200 dark:border-red-800/50">
                                <h4 class="text-lg font-semibold text-red-900 dark:text-red-400 mb-2">Start Fresh
                                    (Delete All Data)</h4>
                                <p class="text-sm text-red-800 dark:text-red-300 mb-4 max-w-2xl leading-relaxed">
                                    This sequence will <strong>permanently delete</strong> all quotes, revenues, stock
                                    adjustments, and purchase orders. It will also reset all stock quantities to zero.
                                    <br><br>
                                    Your user accounts, products, and categorization structures will be preserved. This
                                    action is irreversible.
                                </p>
                                <button @click="showResetModal = true"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 transition shadow-sm">
                                    <x-lucide-alert-triangle class="w-4 h-4 mr-2" /> Start Fresh
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Rate Modal -->
        <div x-show="showRateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="showRateModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <!-- Alpine binds action URL dynamically if editing vs creating -->
                    <form method="POST"
                        :action="editingRateId ? '{{ url("/settings/tax-rates") }}/' + editingRateId : '{{ route("settings.tax-rates.store") }}'"
                        class="p-6">
                        @csrf
                        <template x-if="editingRateId">
                            <input type="hidden" name="_method" value="PATCH">
                        </template>

                        <h2 class="text-lg font-medium text-slate-900 dark:text-white mb-4"
                            x-text="editingRateId ? 'Edit Tax Rate' : 'Create New Tax Rate'"></h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block font-medium text-sm text-slate-700 dark:text-slate-300">Display
                                    Name</label>
                                <input type="text" name="name" x-model="rate_name" required
                                    class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                    placeholder="e.g. GST 18%">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-slate-700 dark:text-slate-300">Rate
                                    Percentage</label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="rate" x-model="rate_value" required
                                        class="mt-1 block w-full pr-8 border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                    <span
                                        class="absolute right-3 top-2.5 text-slate-500 dark:text-slate-400 font-bold text-sm">%</span>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" x-model="rate_is_active"
                                    value="1"
                                    class="rounded border-slate-300 dark:border-slate-600 text-brand-600 shadow-sm focus:ring-brand-500 dark:bg-slate-900">
                                <label for="is_active"
                                    class="ml-2 block text-sm text-slate-900 dark:text-white">Active</label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showRateModal = false"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 transition">Cancel</button>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition"
                                x-text="editingRateId ? 'Update Rate' : 'Create Rate'">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Start Fresh Modal -->
        <div x-show="showResetModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="showResetModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <form action="{{ route('settings.start-fresh') }}" method="POST" class="p-6">
                        @csrf
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center flex-shrink-0">
                                <x-lucide-alert-triangle class="w-5 h-5 text-red-600 dark:text-red-400" />
                            </div>
                            <h2 class="text-lg font-semibold text-red-900 dark:text-red-400">Confirm Data Wipe</h2>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                            Are you absolutely sure? This will permanently wipe all financial transactions, historical
                            stock logs, and quotes. Product inventory numbers will be reset to 0.<br><br>
                            To confirm, type <strong>DELETE</strong> below:
                        </p>
                        <div class="mb-6">
                            <input type="text" x-model="resetConfirm" required
                                class="mt-1 block w-full border-red-300 dark:border-red-700 dark:bg-slate-900 dark:text-slate-300 focus:border-red-500 focus:ring-red-500 text-red-900 dark:text-red-400 font-mono shadow-sm rounded-md"
                                placeholder="Type DELETE">
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="showResetModal = false"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 transition">Cancel</button>
                            <button type="submit" :disabled="resetConfirm !== 'DELETE'"
                                :class="resetConfirm === 'DELETE' ? 'bg-red-600 hover:bg-red-500 active:bg-red-700 cursor-pointer' : 'bg-red-600 opacity-50 cursor-not-allowed'"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition">
                                Permanently Wipe Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>