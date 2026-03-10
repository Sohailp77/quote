<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8 font-sans pb-32">
    <!-- Loading Indicator overlay (Only on Save) -->
    <div wire:loading wire:target="save"
        class="fixed inset-0 bg-slate-900/10 backdrop-blur-sm z-[100] flex items-center justify-center pointer-events-none transition-opacity duration-300">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-xl flex items-center gap-3">
            <x-lucide-loader-2 class="w-6 h-6 text-brand-600 dark:text-brand-400 animate-spin" />
            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Generating Quotation & PDF...</span>
        </div>
    </div>

    <!-- Include Quill -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <style>
        .ql-toolbar {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            background: #f8fafc;
            border-color: #f1f5f9;
        }

        .dark .ql-toolbar {
            background: #1e293b;
            border-color: #0f172a;
        }

        .ql-container {
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
            border-color: #f1f5f9;
            font-family: inherit;
            font-size: 14px;
            min-height: 120px;
        }

        .dark .ql-container {
            border-color: #0f172a;
            color: #cbd5e1;
        }

        .dark .ql-stroke {
            stroke: #94a3b8;
        }

        .dark .ql-fill {
            fill: #94a3b8;
        }

        .dark .ql-picker {
            color: #94a3b8;
        }
    </style>

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Create Quotation</h1>
            <p class="text-sm font-bold text-slate-400 dark:text-slate-500 mt-2">Build a professional quote for your
                customer.</p>
        </div>
    </div>

    @if (session('success'))
        <div
            class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 p-4 rounded-2xl flex items-center justify-between border border-emerald-100 dark:border-emerald-800">
            <span class="font-bold text-sm">{{ session('success') }}</span>
            @if (session('pdf_url'))
                <a href="{{ session('pdf_url') }}" target="_blank"
                    class="flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-emerald-700 transition">
                    <x-lucide-printer class="w-4 h-4" /> View PDF
                </a>
            @endif
        </div>
    @endif

    @if (session('error'))
        <div
            class="mb-6 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-4 rounded-2xl flex items-center justify-between border border-red-100 dark:border-red-800">
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <form wire:submit="save" class="relative">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            <!-- LEFT COLUMN: Main Editor -->
            <div class="xl:col-span-8 space-y-8">

                <!-- 1. Customer Details -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-[32px] shadow-sm border border-slate-200 dark:border-slate-800">
                    <div
                        class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50 rounded-t-[32px]">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-brand-50 dark:bg-brand-900/30 border border-brand-100 dark:border-brand-800 text-brand-600 dark:text-brand-400 rounded-2xl flex items-center justify-center">
                                <x-lucide-user class="w-5 h-5 text-brand-600" />
                            </div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Customer Details</h3>
                        </div>

                        <div>
                            <select wire:model.live="customer_id"
                                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-sm font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 shadow-sm">
                                <option value="">+ Add New Customer</option>
                                @foreach ($customers as $customerOption)
                                    <option value="{{ $customerOption['id'] }}">{{ $customerOption['name'] }}
                                        @if (!empty($customerOption['company'])) ({{ $customerOption['company'] }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" wire:click="clearCustomer"
                                class="ml-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-bold transition-colors">Clear</button>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div x-data="{ 
                            open: false, 
                            search: $wire.entangle('customer_name', true),
                            allCustomers: @js($customers),
                            get filtered() {
                                if (!this.search || this.search.length < 1) return [];
                                const s = this.search.toLowerCase();
                                return this.allCustomers.filter(c => c.name.toLowerCase().includes(s)).slice(0, 5);
                            },
                            select(c) {
                                this.search = c.name;
                                $wire.customer_id = c.id;
                                $wire.call('selectCustomer', c.id);
                                this.open = false;
                            }
                        }" class="relative" wire:ignore>
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2 block">Full
                                Name</label>
                            <div class="relative">
                                <x-lucide-user
                                    class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                                <input type="text" x-model="search" @focus="open = true"
                                    @input="open = true; $wire.customer_id = null;" @keydown.escape="open = false"
                                    required autocomplete="off"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl pl-12 pr-5 py-3.5 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm"
                                    placeholder="Search customer...">
                                <div x-show="open && filtered.length > 0" x-cloak x-transition
                                    class="absolute z-[100] left-0 right-0 mt-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto">
                                    <template x-for="c in filtered" :key="c.id">
                                        <button type="button" @click="select(c)"
                                            class="w-full text-left px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center justify-between group transition-colors border-b border-slate-100 dark:border-slate-800 last:border-none">
                                            <span class="flex flex-col">
                                                <span x-text="c.name"></span>
                                                <template x-if="c.company"><span class="text-[10px] text-slate-400"
                                                        x-text="c.company"></span></template>
                                            </span>
                                            <x-lucide-plus
                                                class="w-4 h-4 text-slate-300 group-hover:text-brand-500 transition-colors" />
                                        </button>
                                    </template>
                                </div>
                            </div>
                            @error('customer_name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2 block">Phone
                                Number</label>
                            <div class="relative">
                                <x-lucide-phone
                                    class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                                <input type="text" wire:model.blur="customer_phone"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl pl-12 pr-5 py-3.5 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm"
                                    placeholder="+91 98765 43210">
                            </div>
                            @error('customer_phone') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2 block">Email
                                Address</label>
                            <div class="relative">
                                <x-lucide-mail
                                    class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                                <input type="email" wire:model.blur="customer_email"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl pl-12 pr-5 py-3.5 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm"
                                    placeholder="contact@example.com">
                            </div>
                            @error('customer_email') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="px-6 pb-6">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2 block">Customer
                            Address</label>
                        <div class="relative">
                            <x-lucide-map-pin class="absolute left-4 top-4 h-4 w-4 text-slate-400" />
                            <textarea wire:model.blur="customer_address" rows="2"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl pl-12 pr-5 py-3 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm resize-none"
                                placeholder="123 Main St, City, State, ZIP"></textarea>
                        </div>
                        @error('customer_address') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 2. Items & Pricing -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-[32px] shadow-sm border border-slate-200 dark:border-slate-800">
                    <div
                        class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50 rounded-t-[32px]">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-brand-50 dark:bg-brand-900/30 border border-brand-100 dark:border-brand-800 text-brand-600 dark:text-brand-400 rounded-2xl flex items-center justify-center">
                                <x-lucide-shopping-cart class="w-5 h-5 text-brand-600" />
                            </div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Items & Pricing</h3>
                        </div>
                        <button type="button" wire:click="addItem"
                            class="inline-flex items-center gap-2 bg-slate-900 dark:bg-slate-700 text-white text-[10px] font-black uppercase tracking-widest px-4 py-2.5 rounded-xl hover:bg-slate-800 dark:hover:bg-slate-600 transition-all shadow-sm">
                            <x-lucide-plus class="w-4 h-4" /> Add Item
                        </button>
                    </div>

                    <div class="p-0">
                        @if (count($items) > 0)
                            <div
                                class="hidden lg:grid grid-cols-12 gap-4 px-6 py-4 bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                <div class="col-span-4">Product Details</div>
                                <div class="col-span-8">
                                    <div
                                        class="grid gap-4 {{ $tax_mode === 'item_level' ? 'grid-cols-6' : 'grid-cols-5' }}">
                                        <div class="col-span-1">Area</div>
                                        <div class="col-span-1">Variant</div>
                                        <div class="col-span-1 text-center">Qty</div>
                                        <div class="col-span-1 text-right">Price</div>
                                        @if ($tax_mode === 'item_level')
                                        <div class="col-span-1">Tax</div> @endif
                                        <div class="col-span-1 text-right">Total</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($items as $index => $item)
                                @php
                                    $productRef = collect($products)->firstWhere('id', $item['product_id']);
                                    $variantRef = collect($productRef['variants'] ?? [])->firstWhere('id', $item['product_variant_id']);
                                    $imagePath = $variantRef['image_path'] ?? ($productRef['image_path'] ?? null);
                                @endphp
                                <div wire:key="item-{{ $item['id'] }}"
                                    class="p-6 relative group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">

                                    <!-- Section Name grouping input -->
                                    <div class="mb-4 flex items-center gap-2 max-w-sm">
                                        <div
                                            class="p-1.5 bg-slate-100 dark:bg-slate-800 rounded flex-shrink-0 text-slate-400">
                                            <x-lucide-layers class="w-3.5 h-3.5" />
                                        </div>
                                        <input type="text" wire:model.blur="items.{{ $index }}.section_name"
                                            placeholder="Section Name (e.g., Living Room)"
                                            class="w-full bg-transparent border-none p-0 text-xs font-bold text-slate-600 dark:text-slate-400 placeholder:text-slate-300 dark:placeholder:text-slate-600 focus:ring-0">
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                                        <div class="lg:col-span-4 flex gap-4">
                                            @if (count($items) > 1)
                                                <button type="button" wire:click="removeItem({{ $index }})"
                                                    class="lg:hidden absolute top-4 right-4 p-2 text-slate-300 hover:text-red-500 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800"><x-lucide-trash-2
                                                        class="w-4 h-4" /></button>
                                            @endif

                                            <div
                                                class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 border-2 border-slate-50 dark:border-slate-700 flex items-center justify-center overflow-hidden shrink-0 mt-1">
                                                <img src="{{ $imagePath ? asset($imagePath) : asset('images/default_product.png') }}"
                                                    class="w-full h-full object-cover">
                                            </div>

                                            <div class="flex-1 min-w-0 pr-8 lg:pr-0">
                                                <select
                                                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl py-3 px-4 text-sm font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-brand-500/20"
                                                    wire:model.live="items.{{ $index }}.product_id" required>
                                                    <option value="">Select Product...</option>
                                                    @foreach ($products as $p)
                                                        <option value="{{ data_get($p, 'id') }}">{{ data_get($p, 'name') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($productRef && !empty($productRef['description']))
                                                    <p
                                                        class="mt-2 text-xs text-slate-400 dark:text-slate-500 line-clamp-1 group-hover:line-clamp-none transition-all">
                                                        {{ $productRef['description'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="lg:col-span-8 flex items-center mt-4 lg:mt-0">
                                            <div
                                                class="w-full grid gap-4 items-center {{ $tax_mode === 'item_level' ? 'grid-cols-2 sm:grid-cols-6' : 'grid-cols-2 sm:grid-cols-5' }}">
                                                <div class="col-span-1">
                                                    <label
                                                        class="lg:hidden block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Area</label>
                                                    @if (isset($productRef['category']['metric_type']) && $productRef['category']['metric_type'] === 'area')
                                                        <input type="number" step="0.01" min="0"
                                                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl py-3 px-3 text-sm font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-brand-500/20"
                                                            wire:model.live.debounce.300ms="items.{{ $index }}.area">
                                                    @else
                                                        <div
                                                            class="h-11 flex items-center justify-center bg-transparent border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-xl text-slate-300 dark:text-slate-600 text-xs font-black">
                                                            N/A</div>
                                                    @endif
                                                </div>

                                                <div class="col-span-1">
                                                    <label
                                                        class="lg:hidden block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Variant</label>
                                                    <select
                                                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl py-3 px-3 text-sm font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-brand-500/20 disabled:opacity-40"
                                                        wire:model.live="items.{{ $index }}.product_variant_id" @if (!$productRef || empty($productRef['variants'])) disabled @endif>
                                                        <option value="">Base</option>
                                                        @if ($productRef && !empty($productRef['variants']))
                                                            @foreach ($productRef['variants'] as $v)
                                                                <option value="{{ data_get($v, 'id') }}">{{ data_get($v, 'name') }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="col-span-1">
                                                    <label
                                                        class="lg:hidden block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Qty</label>
                                                    <input type="number" min="1"
                                                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl py-3 px-3 text-center text-sm font-black text-brand-600 dark:text-brand-400 focus:ring-2 focus:ring-brand-500/20"
                                                        wire:model.live.debounce.300ms="items.{{ $index }}.quantity"
                                                        required>
                                                </div>

                                                <div class="col-span-1">
                                                    <label
                                                        class="lg:hidden block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Price</label>
                                                    <div class="relative">
                                                        <span
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">{{ $currency }}</span>
                                                        <input type="number" min="0" step="0.01"
                                                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl py-3 pl-8 pr-3 text-right text-sm font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-brand-500/20"
                                                            wire:model.live.debounce.300ms="items.{{ $index }}.price"
                                                            required>
                                                    </div>
                                                </div>

                                                @if ($tax_mode === 'item_level')
                                                    <div class="col-span-1">
                                                        <label
                                                            class="lg:hidden block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Tax</label>
                                                        <select
                                                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl py-3 px-3 text-sm font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-brand-500/20"
                                                            wire:model.live="items.{{ $index }}.tax_rate_id">
                                                            <option value="">0%</option>
                                                            @foreach ($taxRates as $tr)
                                                                <option value="{{ data_get($tr, 'id') }}">
                                                                    {{ data_get($tr, 'name') }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif

                                                <div class="col-span-1 flex items-center justify-end gap-3">
                                                    <label
                                                        class="lg:hidden block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Total</label>
                                                    <span class="text-base font-black text-slate-900 dark:text-white">
                                                        <span class="text-slate-400 font-bold mr-0.5">{{ $currency }}</span>
                                                        <span>{{ number_format(floatval($item['price'] ?? 0) * intval($item['quantity'] ?? 1), 2) }}</span>
                                                    </span>
                                                    @if (count($items) > 1)
                                                        <button type="button" wire:click="removeItem({{ $index }})"
                                                            class="hidden lg:flex w-10 h-10 items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-all opacity-0 group-hover:opacity-100"><x-lucide-trash-2
                                                                class="w-4 h-4" /></button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if (count($items) === 0)
                            <div
                                class="text-center py-16 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-3xl m-6">
                                <div
                                    class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300 dark:text-slate-600">
                                    <x-lucide-shopping-cart class="w-8 h-8" />
                                </div>
                                <h3 class="text-sm font-black text-slate-600 dark:text-slate-400 mb-2">No items added</h3>
                                <button type="button" wire:click="addItem"
                                    class="inline-flex items-center gap-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] font-black uppercase tracking-widest px-6 py-3 rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10"><x-lucide-plus
                                        class="w-4 h-4" /> Add First Item</button>
                            </div>
                        @endif
                        @error('items') <div
                            class="m-6 p-4 bg-red-50 text-red-600 text-sm font-bold text-center rounded-2xl border border-red-100 dark:bg-red-900/30 dark:border-red-800">
                            {{ $message }}
                        </div> @enderror

                        @if (count($items) > 0)
                            <div
                                class="flex justify-center p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 rounded-b-[32px]">
                                <button type="button" wire:click="addItem"
                                    class="inline-flex items-center gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[10px] font-black text-slate-600 dark:text-slate-400 uppercase tracking-[0.2em] px-6 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-all shadow-sm"><x-lucide-plus
                                        class="w-4 h-4" /> Add Another Item</button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 3. Notes & Terms -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-[32px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div
                        class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center gap-4 bg-slate-50/50 dark:bg-slate-800/50">
                        <div
                            class="w-10 h-10 bg-sky-50 dark:bg-sky-900/30 border border-sky-100 dark:border-sky-800 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center">
                            <x-lucide-file-text class="w-5 h-5" />
                        </div>
                        <h4 class="font-black text-base text-slate-800 dark:text-slate-200">Notes & Terms</h4>
                    </div>

                    <div class="p-6 space-y-8" wire:ignore>
                        <!-- Rich Text Notes -->
                        <div>
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2 block">Customer
                                Notes</label>
                            <div class="bg-white dark:bg-slate-900 rounded-2xl" x-data="{ content: @entangle('notes') }"
                                x-init="
                                    let quill = new Quill($refs.editor, { theme: 'snow', placeholder: 'Write a note to the customer...' });
                                    quill.root.innerHTML = content;
                                    quill.on('text-change', () => { content = quill.root.innerHTML; });
                                 ">
                                <div x-ref="editor"
                                    class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300"></div>
                            </div>
                        </div>

                        <!-- Rich Text Terms -->
                        <div>
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2 block">Terms
                                and Conditions</label>
                            <div class="bg-white dark:bg-slate-900 rounded-2xl" x-data="{ content: @entangle('terms') }"
                                x-init="
                                    let q = new Quill($refs.editor2, { theme: 'snow', placeholder: 'Standard payment terms...' });
                                    q.root.innerHTML = content;
                                    q.on('text-change', () => { content = q.root.innerHTML; });
                                 ">
                                <div x-ref="editor2"
                                    class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Settings & Payment Summary -->
            <div class="xl:col-span-4 space-y-8">

                <!-- Quote Display Settings -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-[32px] shadow-sm border border-slate-200 dark:border-slate-800">
                    <div
                        class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center gap-4 bg-slate-50/50 dark:bg-slate-800/50 rounded-t-[32px]">
                        <div
                            class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center">
                            <x-lucide-settings-2 class="w-5 h-5" />
                        </div>
                        <h4 class="font-black text-base text-slate-800 dark:text-slate-200">Quote Settings</h4>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Template Selection -->
                        <div>
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3 block">PDF
                                Template</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" wire:click="$set('template_name', 'default')"
                                    class="flex flex-col items-center justify-center p-4 border-2 rounded-2xl transition-all {{ $template_name === 'default' ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/30' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-brand-300' }}">
                                    <div
                                        class="w-8 h-10 bg-slate-200/50 dark:bg-slate-800 rounded flex flex-col gap-1 p-1">
                                        <div class="w-full h-1 bg-slate-300 dark:bg-slate-600 rounded"></div>
                                        <div class="w-full h-full bg-slate-300 dark:bg-slate-600 rounded"></div>
                                    </div>
                                    <span
                                        class="mt-2 text-xs font-bold {{ $template_name === 'default' ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500' }}">Standard</span>
                                </button>
                                <button type="button" wire:click="$set('template_name', 'modern')"
                                    class="flex flex-col items-center justify-center p-4 border-2 rounded-2xl transition-all {{ $template_name === 'modern' ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/30' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-brand-300' }}">
                                    <div class="w-8 h-10 bg-slate-200/50 dark:bg-slate-800 rounded flex gap-1 p-1">
                                        <div class="w-1/2 h-full bg-slate-300 dark:bg-slate-600 rounded"></div>
                                        <div class="w-1/2 h-full bg-brand-300 rounded"></div>
                                    </div>
                                    <span
                                        class="mt-2 text-xs font-bold {{ $template_name === 'modern' ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500' }}">Modern</span>
                                </button>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-slate-800">

                        <!-- Display Toggles -->
                        <div>
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3 block">Display
                                Columns (PDF)</label>
                            <div class="space-y-3">
                                <label class="flex items-center justify-between cursor-pointer group">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Show Product
                                        Images</span>
                                    <div class="relative">
                                        <input type="checkbox" wire:model.live="display_settings.show_images"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600">
                                        </div>
                                    </div>
                                </label>
                                <label class="flex items-center justify-between cursor-pointer group">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Show Item
                                        Descriptions</span>
                                    <div class="relative">
                                        <input type="checkbox" wire:model.live="display_settings.show_description"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600">
                                        </div>
                                    </div>
                                </label>
                                <label class="flex items-center justify-between cursor-pointer group">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Show Tax
                                        Columns</span>
                                    <div class="relative">
                                        <input type="checkbox" wire:model.live="display_settings.show_tax"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600">
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div
                    class="bg-slate-900 dark:bg-slate-800 text-white rounded-[32px] shadow-xl overflow-hidden shadow-slate-900/10">
                    <div class="px-8 py-6 border-b border-white/10 flex items-center gap-4">
                        <div class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center text-white">
                            <x-lucide-credit-card class="w-5 h-5" />
                        </div>
                        <h4 class="font-black text-base">Payment Summary</h4>
                    </div>
                    <div class="p-8 space-y-5">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Subtotal</span>
                            <span class="font-black text-lg"><span
                                    class="text-slate-500 mr-1">{{ $currency }}</span><span>{{ number_format($this->subtotal, 2) }}</span></span>
                        </div>

                        <div class="flex gap-4 items-center">
                            <div class="w-full flex justify-between items-center text-sm border-t border-white/10 pt-5">
                                <div class="flex items-center gap-4">
                                    <span
                                        class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Discount</span>
                                    <div class="relative w-24">
                                        <input type="number" min="0" max="100" step="0.1"
                                            class="w-full bg-black/20 border-none rounded-xl text-white text-right font-black text-sm py-2 pr-8 focus:ring-2 focus:ring-white/20"
                                            wire:model.live.debounce.300ms="discount_percentage">
                                        <span
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 font-black text-xs">%</span>
                                    </div>
                                </div>
                                <span class="text-emerald-400 font-black font-mono">-<span
                                        class="mr-0.5">{{ $currency }}</span><span>{{ number_format($this->discountAmount, 2) }}</span></span>
                            </div>
                        </div>

                        <!-- Delivery Charge -->
                        <div class="flex justify-between items-center text-sm border-t border-white/10 pt-5">
                            <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Delivery Charge</span>
                            <div class="relative w-32">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-xs">{{ $currency }}</span>
                                <input type="number" min="0" step="0.01"
                                    class="w-full bg-black/20 border-none rounded-xl text-white text-right font-black text-sm py-2 px-3 pl-8 focus:ring-2 focus:ring-white/20"
                                    wire:model.live.debounce.300ms="delivery_charge">
                            </div>
                        </div>

                        <!-- Additional Charges -->
                        <div class="space-y-3 border-t border-white/10 pt-5">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Additional Charge</span>
                                <div class="relative w-32">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-xs">{{ $currency }}</span>
                                    <input type="number" min="0" step="0.01"
                                        class="w-full bg-black/20 border-none rounded-xl text-white text-right font-black text-sm py-2 px-3 pl-8 focus:ring-2 focus:ring-white/20"
                                        wire:model.live.debounce.300ms="additional_charge">
                                </div>
                            </div>
                            <input type="text" 
                                class="w-full bg-black/20 border-none rounded-xl text-white text-xs font-bold py-2 px-4 focus:ring-2 focus:ring-white/20 placeholder:text-slate-600"
                                wire:model.live.debounce.300ms="additional_charge_label"
                                placeholder="Reason (e.g. Urgent Handling)">
                        </div>

                        <div class="flex justify-between items-center text-sm border-t border-white/10 pt-5">
                            <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Tax Mode</span>
                            <div
                                class="bg-black/20 p-1 rounded-xl flex text-[10px] font-black uppercase tracking-widest">
                                <button type="button" wire:click="setTaxMode('global')"
                                    class="{{ $tax_mode === 'global' ? 'bg-white text-slate-900 shadow-md' : 'text-slate-400 hover:text-white' }} px-4 py-2 rounded-lg transition-all">Global</button>
                                <button type="button" wire:click="setTaxMode('item_level')"
                                    class="{{ $tax_mode === 'item_level' ? 'bg-white text-slate-900 shadow-md' : 'text-slate-400 hover:text-white' }} px-4 py-2 rounded-lg transition-all">Per
                                    Item</button>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center gap-4">
                                <span
                                    class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">{{ $tax_mode === 'global' ? 'Global Tax %' : 'Total Tax' }}</span>
                                @if ($tax_mode === 'global')
                                    <div class="relative w-24">
                                        <input type="number" step="0.1"
                                            class="w-full bg-black/20 border-none rounded-xl text-white text-right font-black text-sm py-2 pr-8 focus:ring-2 focus:ring-white/20"
                                            wire:model.live.debounce.300ms="gst_rate">
                                        <span
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 font-black text-xs">%</span>
                                    </div>
                                @endif
                            </div>
                            <span class="font-black text-slate-200"><span
                                    class="text-slate-500 mr-1">{{ $currency }}</span><span>{{ number_format($this->taxAmount, 2) }}</span></span>
                        </div>

                        @if (!empty($taxSettings) && isset($taxSettings['strategy']) && $taxSettings['strategy'] === 'split' && $this->taxAmount > 0)
                            <div class="bg-black/20 rounded-2xl p-4 mt-4 border border-white/5">
                                @foreach ($taxSettings['secondary_labels'] as $label)
                                    <div class="flex justify-between items-center mb-2 last:mb-0">
                                        <span
                                            class="text-[10px] font-black uppercase tracking-widest text-slate-400"><span>{{ $label }}</span>
                                            <span class="text-slate-600">(50%)</span></span>
                                        <span class="text-sm font-black text-slate-300"><span
                                                class="text-slate-500">{{ $currency }}</span><span>{{ number_format($this->taxAmount / count($taxSettings['secondary_labels']), 2) }}</span></span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="pt-8 mt-6 border-t border-dashed border-white/20">
                            <div class="flex justify-between items-end">
                                <div>
                                    <span
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Total
                                        Amount</span>
                                    <span class="text-[9px] text-slate-500 font-bold">Including all taxes</span>
                                </div>
                                <span class="text-5xl font-black tracking-tighter text-white drop-shadow-lg"><span
                                        class="text-2xl text-slate-400 align-top mr-1 inline-block mt-2">{{ $currency }}</span><span>{{ number_format($this->totalAmount, 2) }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Action Bar -->
        <div
            class="fixed bottom-0 left-0 right-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl border-t border-slate-200 dark:border-slate-800 p-4 shadow-[0_-10px_40px_rgba(0,0,0,0.04)] z-50">
            <div
                class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="hidden sm:flex items-center gap-8">
                    <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400">
                        <x-lucide-package class="w-5 h-5 text-brand-500" />
                        <span class="text-sm font-bold uppercase tracking-widest"><span
                                class="text-slate-900 dark:text-white font-black text-lg">{{ count($items) }}</span>
                            Items</span>
                    </div>
                    <div class="h-8 w-px bg-slate-200 dark:bg-slate-800"></div>
                    <div
                        class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest flex items-center gap-3">
                        Total: <span class="font-black text-slate-900 dark:text-white text-2xl tracking-tighter"><span
                                class="text-slate-400 mr-1 text-sm align-middle">{{ $currency }}</span><span>{{ number_format($this->totalAmount, 2) }}</span></span>
                    </div>
                </div>
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full sm:w-auto inline-flex justify-center items-center gap-3 px-10 py-4 bg-brand-600 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-brand-700 transition-all shadow-xl shadow-brand-600/30 disabled:opacity-50 disabled:cursor-not-allowed">
                    <x-lucide-printer wire:loading.remove wire:target="save" class="w-5 h-5" />
                    <x-lucide-loader-2 wire:loading wire:target="save" class="w-5 h-5 animate-spin" />
                    <span>Save & Generate PDF</span>
                </button>
            </div>
        </div>
    </form>
</div>