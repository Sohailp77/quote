<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6 relative">
    <!-- Livewire Loading Overlay -->
    <div wire:loading wire:target="save, deleteVariant, image" class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-50 rounded-3xl flex items-center justify-center">
        <x-lucide-loader-2 class="w-8 h-8 text-brand-500 animate-spin" />
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <x-lucide-tag class="w-5 h-5 text-slate-400 dark:text-slate-500" /> Model Variants / Items
            </h3>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-0.5">Add specific colors or designs for this product type.</p>
        </div>
        <button
            wire:click="toggleForm"
            class="inline-flex items-center gap-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-slate-700 dark:hover:bg-slate-200 transition-all focus:ring-2 focus:ring-slate-900/20 dark:focus:ring-white/20 whitespace-nowrap"
        >
            <x-lucide-plus class="w-4 h-4" /> {{ $showForm ? 'Cancel Form' : 'Add Item' }}
        </button>
    </div>

    @if($showForm)
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 mb-5 overflow-hidden">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                    {{ $editingVariantId ? 'Edit Variant' : 'Add New Variant' }}
                </h4>
                <button wire:click="cancelEdit" class="text-sm text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                    Cancel
                </button>
            </div>
            
            <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div class="md:col-span-2 lg:col-span-1">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Name</label>
                    <input type="text" wire:model="name" placeholder="e.g. Alpine White" required class="block w-full bg-white dark:bg-slate-900 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-brand-500/20 rounded-xl shadow-sm text-sm" />
                    @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Image</label>
                    <input type="file" wire:model="image" accept="image/*" class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-200 dark:file:bg-slate-950 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-300 dark:hover:file:bg-slate-900 min-h-[42px] border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900" />
                    @error('image') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">SKU (Optional)</label>
                    <input type="text" wire:model="sku" placeholder="e.g. V-001" class="block w-full bg-white dark:bg-slate-900 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-brand-500/20 rounded-xl shadow-sm text-sm" />
                    @error('sku') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Initial Stock</label>
                    <input type="number" wire:model="stock_quantity" {{ $editingVariantId ? 'disabled' : '' }} placeholder="0" class="block w-full bg-white dark:bg-slate-900 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-brand-500/20 rounded-xl shadow-sm text-sm disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-slate-800" />
                    @error('stock_quantity') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Price Override ({{ $appSettings['currency_symbol'] ?? '₹' }})</label>
                    <input type="number" step="0.01" wire:model="variant_price" placeholder="Leave empty if same" class="block w-full bg-white dark:bg-slate-900 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-brand-500/20 rounded-xl shadow-sm text-sm" />
                    @error('variant_price') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Cost Price ({{ $appSettings['currency_symbol'] ?? '₹' }})</label>
                    <input type="number" step="0.01" wire:model="cost_price" placeholder="0.00" class="block w-full bg-white dark:bg-slate-900 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-brand-500/20 rounded-xl shadow-sm text-sm" />
                    @error('cost_price') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2 lg:col-span-5 flex justify-end mt-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-all focus:outline-none focus:ring-2 focus:ring-brand-500/20 min-w-[120px] h-[42px]">
                        <x-lucide-save class="w-4 h-4" /> <span>{{ $editingVariantId ? 'Update' : 'Save' }}</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($product->variants->isEmpty())
        <div class="text-center py-12 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
            <x-lucide-tag class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600 mb-3" />
            <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400">No variants yet</h3>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Add specific items (colors, designs) for this product type.</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-800 mt-2">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Image</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Item Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">SKU</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sales Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cost Price</th>
                        <th class="px-4 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-100 dark:divide-slate-800/50">
                    @foreach($product->variants as $variant)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3">
                                @if($variant->image_path)
                                    <img src="{{ asset($variant->image_path) }}" alt="{{ $variant->name }}" class="w-10 h-10 rounded-xl object-cover" />
                                @else
                                    <div class="w-10 h-10 bg-slate-100 dark:bg-slate-950 rounded-xl flex items-center justify-center">
                                        <x-lucide-package class="w-5 h-5 text-slate-400 dark:text-slate-500" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-white">{{ $variant->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $variant->sku ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $variant->stock_quantity > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 dark:bg-red-800/30 text-red-700 dark:text-red-400' }}">
                                    {{ $variant->stock_quantity }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($variant->variant_price)
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $appSettings['currency_symbol'] ?? '₹' }}{{ number_format($variant->variant_price, 2) }}</span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic">Base</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($variant->cost_price)
                                    <span class="font-bold text-slate-600 dark:text-slate-400">{{ $appSettings['currency_symbol'] ?? '₹' }}{{ number_format($variant->cost_price, 2) }}</span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic">Base</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button 
                                        wire:click="editVariant({{ $variant->id }})"
                                        class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all"
                                    >
                                        <x-lucide-edit class="w-4 h-4" />
                                    </button>
                                    <button 
                                        wire:click="deleteVariant({{ $variant->id }})" 
                                        wire:confirm="Are you sure you want to delete this variant?"
                                        class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-all"
                                    >
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', (el, component) => {
                
            });
        });
    </script>
</div>
