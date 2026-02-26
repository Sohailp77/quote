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
                        <select wire:model.live="selectedProductString"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-100 transition focus:outline-none min-h-[42px]"
                            required>
                            <option value="">Choose a product...</option>
                            @foreach($products as $p)
                                @if($p->variants && $p->variants->isNotEmpty())
                                    <optgroup label="{{ $p->name }}">
                                        <option value="{{ json_encode(['product_id' => $p->id, 'variant_id' => null]) }}">
                                            {{ $p->name }} (Base) (Stock: {{ $p->stock_quantity }})
                                        </option>
                                        @foreach($p->variants as $v)
                                            <option value="{{ json_encode(['product_id' => $p->id, 'variant_id' => $v->id]) }}">
                                                {{ $p->name }} - {{ $v->name }} (Stock: {{ $v->stock_quantity }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    <option value="{{ json_encode(['product_id' => $p->id, 'variant_id' => null]) }}">
                                        {{ $p->name }} (Stock: {{ $p->stock_quantity }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
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
                            <input type="number" step="0.01" wire:model="unit_cost"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-slate-900 transition focus:outline-none min-h-[42px]"
                                placeholder="0.00" required />
                            @error('unit_cost') <span class="text-xs text-red-500 mt-2 font-bold ml-1">{{ $message }}</span>
                            @enderror
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