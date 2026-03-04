<div>
    @php
        $baseStock = $product->stock_quantity ?? 0;
        $variantsStock = clone $product->variants;
        $variantsStockCount = $variantsStock->sum('stock_quantity') ?? 0;
        $totalStock = $baseStock + $variantsStockCount;
        $hasVariants = $product->variants->isNotEmpty();
        $isLow = $totalStock <= 5;
        $isOut = $totalStock === 0;
    @endphp

    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6 mb-5 relative">
        <!-- Livewire Loading Overlay -->
        <div wire:loading class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-50 rounded-3xl flex items-center justify-center">
            <x-lucide-loader-2 class="w-8 h-8 text-brand-500 animate-spin" />
        </div>

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <x-lucide-package class="w-5 h-5 text-slate-400 dark:text-slate-500" /> Stock Management
            </h3>
            <span class="text-xs font-bold px-3 py-1.5 rounded-full {{ $isOut ? 'bg-red-100 dark:bg-red-800/30 text-red-700 dark:text-red-400' : ($isLow ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400') }}">
                {{ $isOut ? 'Out of Stock' : ($isLow ? 'Low Stock' : 'In Stock') }}
            </span>
        </div>

        <div class="flex items-end gap-2 mb-2">
            <div class="text-5xl font-black text-slate-900 dark:text-white">{{ $totalStock }}</div>
            <div class="text-slate-400 dark:text-slate-500 text-sm mb-2">total units in stock</div>
        </div>

        @if($hasVariants)
            <div class="flex gap-4 mb-6 text-sm text-slate-500 dark:text-slate-400 font-medium">
                <div class="bg-slate-50 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-slate-800/50">
                    Base: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $baseStock }}</span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-slate-800/50">
                    Variants: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $variantsStockCount }}</span>
                </div>
            </div>
        @else
            <div class="mb-6"></div>
        @endif

        @if($isLow)
            <div class="flex items-center gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl px-4 py-2.5 mb-5 text-sm text-amber-700 dark:text-amber-400">
                <x-lucide-alert-triangle class="w-4 h-4 flex-shrink-0" />
                {{ $isOut ? 'This product is out of stock. Restock immediately.' : "Low stock warning — only {$totalStock} units remaining." }}
            </div>
        @endif

        @if($isBoss)
            <form wire:submit="save" class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 mb-5">
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Manual Adjustment</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Transaction Type</label>
                        <select
                            wire:model.live="transactionType"
                            class="w-full bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 min-h-[42px]"
                        >
                            <option value="adjustment">Standard Adjustment</option>
                            <option value="sale">External Sale (Revenue)</option>
                            <option value="loss">Damage/Loss (Cost)</option>
                            <option value="purchase">External Purchase (Cost)</option>
                            <option value="reversion">Reversion/Correction</option>
                            <option value="initial_stock">Initial Stock</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Target Item</label>
                        <select
                            wire:model="product_variant_id"
                            class="w-full bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 min-h-[42px]"
                        >
                            <option value="">Base Product</option>
                            @foreach($product->variants as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    @if($transactionType === 'sale')
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Total Revenue ({{ $appSettings['currency_symbol'] ?? '₹' }})</label>
                            <input
                                type="number"
                                step="0.01"
                                wire:model="amount"
                                placeholder="Total sale amount"
                                required
                                class="w-full bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 min-h-[42px]"
                            />
                        </div>
                    @else
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ $transactionType === 'adjustment' ? 'Unit Cost (' . ($appSettings['currency_symbol'] ?? '₹') . ') (Optional)' : 'Unit Cost (' . ($appSettings['currency_symbol'] ?? '₹') . ')' }}</label>
                            <input
                                type="number"
                                step="0.01"
                                wire:model="unit_cost"
                                placeholder="0.00"
                                {{ $transactionType !== 'adjustment' ? 'required' : '' }}
                                class="w-full bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 min-h-[42px]"
                            />
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Quantity</label>
                        <div class="flex bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-brand-500/20 transition min-h-[42px]">
                            <button
                                type="button"
                                wire:click="$set('direction', 'add')"
                                class="px-3 py-2 text-xs font-bold transition {{ $direction === 'add' ? 'bg-emerald-500 text-white' : 'text-slate-400 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' }}"
                            >
                                Add (+)
                            </button>
                            <input
                                type="number"
                                min="1"
                                wire:model="quantity"
                                placeholder="Qty"
                                required
                                class="w-full border-none px-3 py-2 text-sm focus:ring-0 text-center font-bold bg-transparent dark:text-white"
                            />
                            <button
                                type="button"
                                wire:click="$set('direction', 'deduct')"
                                class="px-3 py-2 text-xs font-bold transition {{ $direction === 'deduct' ? 'bg-red-500 text-white' : 'text-slate-400 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800' }}"
                            >
                                Less (-)
                            </button>
                        </div>
                        @error('quantity') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Reason / Comment</label>
                        <input
                            type="text"
                            wire:model="reason"
                            placeholder="e.g. Received shipment, Manual count"
                            required
                            class="w-full bg-white dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 min-h-[42px]"
                        />
                        @error('reason') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="bg-slate-900 dark:bg-brand-500 text-white dark:text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-800 dark:hover:bg-brand-600 transition h-[42px]">
                        Apply
                    </button>
                </div>
            </form>
        @endif

        <div class="mt-8 border-t border-slate-100 dark:border-slate-800 pt-7">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <x-lucide-history class="w-3.5 h-3.5" /> Adjustment History
                </h4>
                
                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" />
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search reason..." 
                            class="pl-9 pr-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-[11px] focus:ring-1 focus:ring-brand-500 transition-all w-full sm:w-40" />
                    </div>
                    <select wire:model.live="filterType" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg py-1.5 px-3 text-[11px] focus:ring-1 focus:ring-brand-500 transition-all">
                        <option value="">All Types</option>
                        <option value="quote">Quote</option>
                        <option value="return">Return</option>
                        <option value="manual">Manual</option>
                        <option value="adjustment">Adjustment</option>
                        <option value="sale">Sale</option>
                        <option value="loss">Loss</option>
                        <option value="purchase">Purchase</option>
                        <option value="reversion">Reversion</option>
                        <option value="initial_stock">Initial Stock</option>
                    </select>
                    <select wire:model.live="filterUserId" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg py-1.5 px-3 text-[11px] focus:ring-1 focus:ring-brand-500 transition-all">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($adjustments->isNotEmpty())
                <div class="space-y-4">
                    @foreach($adjustments as $adj)
                        @php
                            $isReversion = $adj->type === 'reversion';
                        @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-2xl border transition {{ $isReversion ? 'bg-slate-50/50 dark:bg-slate-800/30 border-slate-100 dark:border-slate-800/50 opacity-60' : 'bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800 shadow-sm' }}">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-2 sm:mb-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 {{ $adj->quantity_change > 0 ? 'bg-emerald-50 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400' : 'bg-red-50 dark:bg-red-900 text-red-600 dark:text-red-400' }}">
                                    @if($adj->quantity_change > 0)
                                        <x-lucide-plus class="w-4 h-4" />
                                    @else
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-slate-900 dark:text-white">{{ $adj->quantity_change > 0 ? '+' : '' }}{{ $adj->quantity_change }}</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-100 dark:border-slate-700">
                                            {{ str_replace('_', ' ', $adj->type) }}
                                        </span>
                                        @if($adj->variant)
                                            <span class="text-[10px] font-bold text-sky-600 dark:text-sky-400">({{ $adj->variant->name }})</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-[200px] sm:max-w-md leading-relaxed mt-1">{{ $adj->reason }}</p>
                                    @if($adj->reverted_at)
                                        <span class="inline-flex mt-1 text-[8px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-950 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700">
                                            Reverted
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto mt-2 sm:mt-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left sm:text-right">
                                    <p class="font-bold text-slate-700 dark:text-slate-300 text-sm sm:text-base">{{ $adj->created_at->format('n/j/Y') }}</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">By {{ $adj->user->name ?? 'Unknown' }}</p>
                                </div>
                                @if($isBoss && !$isReversion && !$adj->reverted_at)
                                    <button 
                                        wire:click="revert({{ $adj->id }})" 
                                        wire:confirm="Are you sure you want to revert this adjustment? This will undo the stock change and associated revenue/costs."
                                        class="text-[10px] font-black text-red-600 dark:text-red-400 uppercase tracking-widest hover:bg-red-50 dark:hover:bg-red-900/30 px-2 py-1.5 rounded transition border border-transparent hover:border-red-100 dark:hover:border-red-800"
                                    >
                                        Revert
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="mt-6 border-t border-slate-50 dark:border-slate-800 pt-4">
                    {{ $adjustments->links(data: ['scrollTo' => false]) }}
                </div>
            @else
                <div class="mt-10 text-center py-10 bg-slate-50/50 dark:bg-slate-800/30 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700">
                    <x-lucide-history class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600 mb-3" />
                    <p class="text-sm text-slate-400 dark:text-slate-500 font-medium">No adjustment history found matching your filters.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', (el, component) => {
                
            });
        });
    </script>
</div>
