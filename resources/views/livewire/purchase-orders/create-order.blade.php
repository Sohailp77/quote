<div>
    <!-- Button to trigger modal -->
    <button wire:click="openModal"
        class="flex items-center gap-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-5 py-2.5 rounded-full text-sm font-bold shadow-lg shadow-slate-900/20 hover:bg-slate-800 dark:hover:bg-slate-200 transition">
        <x-lucide-plus class="w-4 h-4" /> New Reorder
    </button>

    <!-- Create Modal via Livewire -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div
                class="bg-white dark:bg-slate-900 rounded-[40px] w-full max-w-lg p-10 shadow-2xl border border-slate-100 dark:border-slate-800 transform transition-all relative">

                <!-- Livewire Loading Overlay within Modal -->
                <div wire:loading wire:target="save"
                    class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-50 rounded-[40px] flex items-center justify-center">
                    <x-lucide-loader-2 class="w-8 h-8 text-brand-500 animate-spin" />
                </div>

                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-slate-900 dark:bg-white rounded-2xl flex items-center justify-center text-white dark:text-slate-900 shadow-lg shadow-slate-900/20">
                            <x-lucide-package class="w-6 h-6" />
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">New Reorder</h3>
                    </div>
                    <button wire:click="closeModal"
                        class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-800 transition text-slate-400 dark:text-slate-500">
                        <x-lucide-plus class="w-6 h-6 rotate-45" />
                    </button>
                </div>

                <form wire:submit="save" class="space-y-6">
                    <div>
                        <label
                            class="block text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 ml-1">Select
                            Product</label>
                        <div x-data="{
                            open: false,
                            search: '',
                            value: @entangle('selectedProductString').live,
                            options: [
                                @foreach($products as $p)
                                    @if($p->variants && $p->variants->isNotEmpty())
                                        {
                                            label: '{{ addslashes($p->name) }} (Base)',
                                            badge: 'Stock: {{ $p->stock_quantity }}',
                                            group: '{{ addslashes($p->name) }}',
                                            value: '{{ addslashes(json_encode(['product_id' => $p->id, 'variant_id' => null])) }}',
                                            isLow: {{ $p->isLowStock() ? 'true' : 'false' }}
                                        },
                                        @foreach($p->variants as $v)
                                            {
                                                label: '{{ addslashes($p->name) }} - {{ addslashes($v->name) }}',
                                                badge: 'Stock: {{ $v->stock_quantity }}',
                                                group: '{{ addslashes($p->name) }}',
                                                value: '{{ addslashes(json_encode(['product_id' => $p->id, 'variant_id' => $v->id])) }}',
                                                isLow: {{ $v->isLowStock() ? 'true' : 'false' }}
                                            },
                                        @endforeach
                                    @else
                                        {
                                            label: '{{ addslashes($p->name) }}',
                                            badge: 'Stock: {{ $p->stock_quantity }}',
                                            group: null,
                                            value: '{{ addslashes(json_encode(['product_id' => $p->id, 'variant_id' => null])) }}',
                                            isLow: {{ $p->isLowStock() ? 'true' : 'false' }}
                                        },
                                    @endif
                                @endforeach
                            ],
                            get filteredOptions() {
                                if (this.search === '') {
                                    return this.options;
                                }
                                return this.options.filter(item => {
                                    return item.label.toLowerCase().includes(this.search.toLowerCase());
                                });
                            },
                            get selectedLabel() {
                                let selectedItem = this.options.find(i => i.value === this.value);
                                return selectedItem ? selectedItem.label : 'Search and select a product...';
                            },
                            selectOption(val) {
                                this.value = val;
                                this.open = false;
                                this.search = '';
                            }
                        }" class="relative w-full" @click.away="open = false">
                            
                            <!-- Dropdown Trigger button -->
                            <button type="button" @click="open = !open" 
                                class="w-full flex items-center justify-between bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-slate-900 transition focus:outline-none min-h-[42px] text-left">
                                <span x-text="selectedLabel" :class="value === '' ? 'text-slate-400' : ''"></span>
                                <x-lucide-chevron-down class="w-4 h-4 text-slate-400" />
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" x-cloak x-transition.opacity
                                class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-72">
                                
                                <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="relative">
                                        <x-lucide-search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                        <input type="text" x-model="search" placeholder="Type to search..." 
                                            class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl pl-9 pr-3 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 placeholder:text-slate-400">
                                    </div>
                                </div>

                                <div class="overflow-y-auto p-2 space-y-1">
                                    <template x-for="(item, index) in filteredOptions" :key="index">
                                        <button type="button" @click="selectOption(item.value)" 
                                            class="w-full text-left px-3 py-2 rounded-xl text-sm transition-colors flex items-center justify-between group"
                                            :class="value === item.value ? 'bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                            <div class="flex items-center gap-2">
                                                <span x-text="item.label" class="font-semibold"></span>
                                                <template x-if="item.isLow">
                                                    <span class="px-1.5 py-0.5 rounded flex items-center gap-1 text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400">
                                                        <x-lucide-alert-triangle class="w-3 h-3" /> Low Stock
                                                    </span>
                                                </template>
                                            </div>
                                            <span x-text="item.badge" class="text-xs font-mono text-slate-400 dark:text-slate-500"></span>
                                        </button>
                                    </template>
                                    
                                    <div x-show="filteredOptions.length === 0" class="p-4 text-center text-sm text-slate-400">
                                        No products found.
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('product_id') <span class="text-xs text-red-500 mt-2 font-bold ml-1">{{ $message }}</span>
                        @enderror
                        @error('product_variant_id') <span
                        class="text-xs text-red-500 mt-2 font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 ml-1">Quantity</label>
                            <input type="number" wire:model="quantity"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-slate-900 transition focus:outline-none min-h-[42px]"
                                placeholder="0" required />
                            @error('quantity') <span class="text-xs text-red-500 mt-2 font-bold ml-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="block text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 ml-1">Unit
                                Cost ({{ $appSettings['currency_symbol'] ?? '₹' }})</label>
                            <input type="number" step="0.01" wire:model.live="unit_cost"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-slate-900 transition focus:outline-none min-h-[42px]"
                                placeholder="0.00" required />
                            @error('unit_cost') <span class="text-xs text-red-500 mt-2 font-bold ml-1">{{ $message }}</span>
                            @enderror
                            
                            <div class="mt-3 ml-1 flex items-center gap-2">
                                <input type="checkbox" id="update_cost_price" wire:model="update_cost_price" class="rounded text-slate-900 focus:ring-slate-900 dark:bg-slate-800 dark:border-slate-700 dark:checked:bg-white dark:checked:text-slate-900">
                                <label for="update_cost_price" class="text-xs font-semibold text-slate-500 dark:text-slate-400 cursor-pointer">Update product catalog price</label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 ml-1">Est.
                            Arrival</label>
                        <input type="date" wire:model="estimated_arrival"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-slate-900 transition focus:outline-none min-h-[42px]" />
                        @error('estimated_arrival') <span
                        class="text-xs text-red-500 mt-2 font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black py-5 rounded-[24px] shadow-xl shadow-slate-900/20 dark:shadow-white/10 hover:bg-slate-800 dark:hover:bg-slate-200 transition transform hover:-translate-y-0.5 active:translate-y-0 min-h-[42px]">
                            Place Reorder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', (el, component) => {
                
            });
        });
    </script>
</div>