<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-6 lg:py-8">
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
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show"
                    class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl relative flex justify-between items-center shadow-sm transition-all duration-300">
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
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
                            <button wire:click="switchTab('{{ $item['id'] }}')"
                                class="flex items-center w-full px-4 py-3 text-sm font-semibold rounded-2xl transition-all {{ $activeTab === $item['id'] ? (isset($item['danger']) && $item['danger'] ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-900 dark:bg-brand-500 text-white shadow-sm') : (isset($item['danger']) && $item['danger'] ? 'text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-200') }}">
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
                        @if($activeTab === 'general')
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                Company Profile</h3>
                            <form wire:submit="saveGeneral" class="space-y-6 max-w-xl">
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="company_name">Company Name</label>
                                    <input type="text" wire:model="company_name" id="company_name" required
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm">
                                    @error('company_name')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="company_email">Official Email</label>
                                    <input type="email" wire:model="company_email" id="company_email"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="company_phone">Phone Number</label>
                                    <input type="text" wire:model="company_phone" id="company_phone"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="gstin">GSTIN / Tax ID</label>
                                    <input type="text" wire:model="gstin" id="gstin"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="currency_symbol">Currency Symbol</label>
                                    <select wire:model="currency_symbol" id="currency_symbol"
                                        class="mt-1 block w-full max-w-[120px] border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                        @foreach(['$' => '$ (USD)', '€' => '€ (EUR)', '£' => '£ (GBP)', '₹' => '₹ (INR)', '﷼' => '﷼ (SAR)', 'د.إ' => 'د.إ (AED)'] as $sym => $label)
                                            <option value="{{ $sym }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Used on invoices, dashboard,
                                        and quotes.</p>
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="company_address">Address</label>
                                    <textarea wire:model="company_address" id="company_address" rows="3"
                                        class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 dark:text-slate-300 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm"></textarea>
                                </div>
                                <div
                                    class="flex items-center gap-4 border-t border-slate-100 dark:border-slate-800 pt-4 mt-6">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 dark:hover:bg-brand-400 disabled:opacity-50 transition ease-in-out duration-150">
                                        Save Profile
                                        <div wire:loading wire:target="saveGeneral"
                                            class="ml-2 w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin">
                                        </div>
                                    </button>
                                </div>
                            </form>
                        @endif

                        <!-- BANK DETAILS TAB -->
                        @if($activeTab === 'bank')
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                Bank Details</h3>
                            <p class="text-sm text-slate-400 dark:text-slate-500 mb-6">These details appear on the bottom of
                                every PDF quotation.</p>
                            <form wire:submit="saveBank" class="space-y-6 max-w-xl">
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_name">Bank Name</label>
                                    <input type="text" wire:model="bank_name" id="bank_name" placeholder="e.g. HDFC Bank"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_account_name">Account Name</label>
                                    <input type="text" wire:model="bank_account_name" id="bank_account_name"
                                        placeholder="As per bank records"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_account_number">Account Number</label>
                                    <input type="text" wire:model="bank_account_number" id="bank_account_number"
                                        placeholder="e.g. 50200012345678"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_ifsc">IFSC / SWIFT Code</label>
                                    <input type="text" wire:model="bank_ifsc" id="bank_ifsc" placeholder="e.g. HDFC0001234"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_branch">Branch</label>
                                    <input type="text" wire:model="bank_branch" id="bank_branch"
                                        placeholder="e.g. Main Branch, New Delhi"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="bank_qr_code">Payment QR Code Image (Optional)</label>
                                    <input type="file" wire:model="bank_qr_code" id="bank_qr_code" accept="image/*"
                                        class="mt-1 block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                                    @if($bank_qr_code)
                                        <div class="mt-2 text-sm text-brand-600">Preview Available</div>
                                    @elseif($existing_bank_qr_code)
                                        <div class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">
                                            A QR code is currently uploaded. <a
                                                href="{{ asset('storage/' . $existing_bank_qr_code) }}" target="_blank"
                                                class="underline">View QR Code</a>
                                        </div>
                                    @endif
                                    @error('bank_qr_code')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                                </div>
                                <div
                                    class="flex items-center gap-4 border-t border-slate-100 dark:border-slate-800 pt-4 mt-6">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 dark:hover:bg-brand-400 transition ease-in-out duration-150">
                                        Save Bank Details
                                    </button>
                                </div>
                            </form>
                        @endif

                        <!-- THEME TAB -->
                        @if($activeTab === 'theme')
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                Brand & Appearance</h3>
                            <p class="text-sm text-slate-400 dark:text-slate-500 mb-6">Customize the look and feel of your
                                application and PDF quotes.</p>
                            <form wire:submit="saveTheme" class="space-y-6 max-w-xl">
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300">Theme
                                        Mode</label>
                                    <div class="mt-2 flex gap-4">
                                        @foreach(['light', 'dark', 'system'] as $m)
                                            <div class="flex items-center">
                                                <input type="radio" id="theme_{{ $m }}" wire:model="theme_mode" value="{{ $m }}"
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
                                        <input type="color" wire:model.live="brand_color_primary" id="brand_color_primary"
                                            class="h-10 w-10 border-0 p-0 rounded-lg cursor-pointer flex-shrink-0">
                                        <input type="text" wire:model="brand_color_primary"
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
                                                <button type="button"
                                                    wire:click="$set('brand_color_primary', '{{ strtolower($c['hex']) }}')"
                                                    class="w-8 h-8 rounded-full border-2 transition-all shadow-sm {{ strtolower($brand_color_primary) === strtolower($c['hex']) ? 'border-slate-900 dark:border-white scale-110 ring-2 ring-slate-400 ring-offset-2 dark:ring-offset-slate-900' : 'border-transparent hover:scale-110' }}"
                                                    style="background-color: {{ $c['hex'] }}" title="{{ $c['name'] }}"></button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @error('brand_color_primary')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 dark:hover:bg-brand-400 transition ease-in-out duration-150">
                                        Save Theme Attributes
                                    </button>
                                </div>
                            </form>
                        @endif

                        <!-- TAX CONFIGURATION TAB -->
                        @if($activeTab === 'tax_config')
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                Tax Configuration</h3>
                            <form wire:submit="saveTaxConfig" class="space-y-6 max-w-xl">
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300">Tax
                                        Strategy</label>
                                    <div class="mt-2 space-y-4">
                                        <div class="flex items-center">
                                            <input type="radio" id="strategy_single" wire:model.live="tax_strategy"
                                                value="single"
                                                class="focus:ring-slate-500 h-4 w-4 text-slate-900 dark:text-white border-slate-300 dark:border-slate-600 dark:bg-slate-900">
                                            <label for="strategy_single"
                                                class="ml-3 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                                Single Rate (Standard)
                                                <p class="text-slate-500 dark:text-slate-400 text-xs font-normal">Apply a
                                                    single tax percentage (e.g. VAT 10%)</p>
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="radio" id="strategy_split" wire:model.live="tax_strategy"
                                                value="split"
                                                class="focus:ring-slate-500 h-4 w-4 text-slate-900 dark:text-white border-slate-300 dark:border-slate-600 dark:bg-slate-900">
                                            <label for="strategy_split"
                                                class="ml-3 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                                Split Tax (Dual GST/HST)
                                                <p class="text-slate-500 dark:text-slate-400 text-xs font-normal">Split tax
                                                    into components (e.g. CGST + SGST)</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="tax_primary_label">Primary Tax Label</label>
                                    <input type="text" wire:model="tax_primary_label" id="tax_primary_label"
                                        placeholder="e.g. GST"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>

                                @if($tax_strategy === 'split')
                                    <div
                                        class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                                        <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Split
                                            Components Labels</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <input type="text" wire:model="tax_secondary_labels.0"
                                                placeholder="Component 1 (e.g. CGST)"
                                                class="block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                            <input type="text" wire:model="tax_secondary_labels.1"
                                                placeholder="Component 2 (e.g. SGST)"
                                                class="block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Currently supports 2-way
                                            split (50/50) only.</p>
                                    </div>
                                @endif

                                <div
                                    class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800 mt-6">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 dark:hover:bg-brand-400 transition">
                                        Update Configuration
                                    </button>
                                </div>
                            </form>
                        @endif

                        <!-- TAX RATES TAB -->
                        @if($activeTab === 'tax_rates')
                            <div
                                class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Tax Rates Table</h3>
                                <button wire:click="openRateModal"
                                    class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 dark:hover:bg-brand-400 transition">
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
                                                    {{ $rate->name }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                                    {{ $rate->rate }}%
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rate->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-400' }}">
                                                        {{ $rate->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button wire:click="openRateModal({{ $rate->id }})"
                                                        class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all mr-2">
                                                        <x-lucide-edit-2 class="w-4 h-4" />
                                                    </button>
                                                    <button wire:click="deleteRate({{ $rate->id }})"
                                                        onclick="confirm('Are you sure you want to delete this tax rate?') || event.stopImmediatePropagation()"
                                                        class="p-1.5 text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all">
                                                        <x-lucide-trash-2 class="w-4 h-4" />
                                                    </button>
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
                        @endif

                        <!-- BUSINESS GOALS TAB -->
                        @if($activeTab === 'goals')
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                                Business Goals & Targets</h3>
                            <p class="text-sm text-slate-400 dark:text-slate-500 mb-6">Set performance targets to track your
                                progress on the analytics dashboard.</p>
                            <form wire:submit="saveGoals" class="space-y-6 max-w-xl">
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="monthly_revenue_goal">Monthly Revenue Goal (₹)</label>
                                    <input type="number" wire:model="monthly_revenue_goal" id="monthly_revenue_goal"
                                        placeholder="e.g. 500000"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="conversion_rate_goal">Conversion Rate Target (%)</label>
                                    <input type="number" step="0.1" wire:model="conversion_rate_goal"
                                        id="conversion_rate_goal" placeholder="e.g. 25"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300"
                                        for="monthly_stock_cost_budget">Monthly Stock Budget (₹)</label>
                                    <input type="number" wire:model="monthly_stock_cost_budget"
                                        id="monthly_stock_cost_budget" placeholder="e.g. 200000"
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                </div>
                                <div
                                    class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800 mt-6">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 dark:hover:bg-brand-400 transition">
                                        Save Goals
                                    </button>
                                </div>
                            </form>
                        @endif

                        <!-- DANGER ZONE TAB -->
                        @if($activeTab === 'danger')
                            <h3
                                class="text-xl font-bold text-red-600 dark:text-red-400 border-b border-red-100 dark:border-red-800 pb-4 mb-6">
                                Danger Zone</h3>
                            <div
                                class="p-6 bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-200 dark:border-red-800/50">
                                <h4 class="text-lg font-semibold text-red-900 dark:text-red-400 mb-2">Start Fresh (Delete
                                    All Data)</h4>
                                <p class="text-sm text-red-800 dark:text-red-300 mb-4 max-w-2xl leading-relaxed">
                                    This sequence will <strong>permanently delete</strong> all quotes, revenues, stock
                                    adjustments, and purchase orders. It will also reset all stock quantities to zero.
                                    <br><br>
                                    Your user accounts, products, and categorization structures will be preserved. This
                                    action is irreversible.
                                </p>
                                <button wire:click="$set('showResetModal', true)"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 transition shadow-sm">
                                    <x-lucide-alert-triangle class="w-4 h-4 mr-2" /> Start Fresh
                                </button>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Rate Modal -->
        @if($showRateModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"
                        wire:click="$set('showRateModal', false)"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div
                        class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <form wire:submit="saveRate" class="p-6">
                            <h2 class="text-lg font-medium text-slate-900 dark:text-white mb-4">
                                {{ $editingRateId ? 'Edit Tax Rate' : 'Create New Tax Rate' }}
                            </h2>

                            <div class="space-y-4">
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300">Display
                                        Name</label>
                                    <input type="text" wire:model="rate_name" required
                                        class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                                        placeholder="e.g. GST 18%">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-slate-700 dark:text-slate-300">Rate
                                        Percentage</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" wire:model="rate_value" required
                                            class="mt-1 block w-full pr-8 border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                        <span
                                            class="absolute right-3 top-2.5 text-slate-500 dark:text-slate-400 font-bold text-sm">%</span>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_active" wire:model="rate_is_active"
                                        class="rounded border-slate-300 dark:border-slate-600 text-brand-600 shadow-sm focus:ring-brand-500 dark:bg-slate-900">
                                    <label for="is_active"
                                        class="ml-2 block text-sm text-slate-900 dark:text-white">Active</label>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" wire:click="$set('showRateModal', false)"
                                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition">
                                    {{ $editingRateId ? 'Update Rate' : 'Create Rate' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Start Fresh Modal -->
        @if($showResetModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"
                        wire:click="$set('showResetModal', false)"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div
                        class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                        <form wire:submit="startFresh" class="p-6">
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
                                <input type="text" wire:model.live="resetConfirm" required
                                    class="mt-1 block w-full border-red-300 dark:border-red-700 dark:bg-slate-900 dark:text-slate-300 focus:border-red-500 focus:ring-red-500 text-red-900 dark:text-red-400 font-mono shadow-sm rounded-md"
                                    placeholder="Type DELETE">
                            </div>
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button" wire:click="$set('showResetModal', false)"
                                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 transition">Cancel</button>
                                @if($resetConfirm === 'DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 transition">
                                        Permanently Wipe Data
                                    </button>
                                @else
                                    <button type="button" disabled
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest opacity-50 cursor-not-allowed">
                                        Permanently Wipe Data
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Re-initialize Lucide Icons when Livewire updates -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('init-lucide', () => {
                setTimeout(() => {
                    
                }, 10);
            });
        });
    </script>
</div>