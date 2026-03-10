<x-app-layout>
    <div x-data="productForm({{ Js::from($categories) }})">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('products.index') }}"
                        class="p-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <x-lucide-arrow-left class="w-4 h-4" /></i>
                    </a>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Add New Product Type</h1>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Fill in the details below to create a new product.
                </p>
            </div>
        </div>

        <div class="max-w-2xl">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6 md:p-8">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    <div>
                        <label for="category_id"
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Category</label>
                        <select id="category_id" name="category_id" x-model="categoryId"
                            class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                            autofocus>
                            <option value="">Select a Category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }} ({{ $category->unit_name }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div>
                        <label for="name"
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Product Type
                            Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                            :placeholder="selectedCategory ? `e.g. 60x60 ${selectedCategory.name}` : 'e.g. 60x60 Premium Tiles'" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="price"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                Sales Price per <span x-text="selectedCategory ? selectedCategory.unit_name : 'Unit'"></span>
                                ({{ $currency ?? '₹' }})
                            </label>
                            <input id="price" type="number" name="price" step="0.01" value="{{ old('price') }}"
                                class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                                placeholder="0.00" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <div>
                            <label for="cost_price"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                Cost Price per <span x-text="selectedCategory ? selectedCategory.unit_name : 'Unit'"></span>
                                ({{ $currency ?? '₹' }})
                            </label>
                            <input id="cost_price" type="number" name="cost_price" step="0.01" value="{{ old('cost_price') }}"
                                class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                                placeholder="0.00" title="Used for profit calculation" />
                            <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
                            <p class="text-[10px] text-slate-400 mt-1">Used to calculate profit in quotations.</p>
                        </div>

                        <template x-if="selectedCategory && selectedCategory.metric_type !== 'fixed'">
                            <div
                                class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-xl border border-blue-100 dark:border-blue-800/50">
                                <div class="flex items-center mb-2 gap-2">
                                    <label for="unit_size"
                                        class="block text-sm font-bold text-blue-900 dark:text-blue-200"
                                        x-text="selectedCategory?.metric_type === 'area' ? 'Coverage Area' : 'Weight'"></label>
                                    <x-lucide-info class="w-4 h-4 text-blue-400 dark:text-blue-500" /></i>
                                </div>
                                <div class="flex items-center">
                                    <input id="unit_size" type="number" name="unit_size" step="0.01"
                                        value="{{ old('unit_size') }}"
                                        class="block w-full min-h-[42px] bg-white dark:bg-slate-900 border border-blue-200 dark:border-blue-700 focus:border-blue-400 dark:focus:border-blue-500 focus:ring-blue-200 dark:focus:ring-blue-800 rounded-l-xl rounded-r-none text-sm"
                                        :placeholder="selectedCategory?.metric_type === 'area' ? '1.44' : '50'" />
                                    <span
                                        class="inline-flex min-h-[42px] items-center px-3 py-2 border border-l-0 border-blue-200 dark:border-blue-700 bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-200 text-sm font-medium rounded-r-xl rounded-l-none"
                                        x-text="selectedCategory?.metric_type === 'area' ? `sq.m / ${selectedCategory?.unit_name}` : `kg / ${selectedCategory?.unit_name}`">
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->get('unit_size')" class="mt-2" />
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1"
                                    x-text="`Used to calculate total ${selectedCategory?.metric_type === 'area' ? 'area' : 'weight'} in quotations.`">
                                </p>
                            </div>
                        </template>

                        <div
                            :class="selectedCategory && selectedCategory.metric_type !== 'fixed' ? '' : 'md:col-span-2'">
                            <label for="sku"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">SKU
                                (Optional)</label>
                            <input id="sku" type="text" name="sku" value="{{ old('sku') }}"
                                class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                                placeholder="e.g. TYP-001" />
                            <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <label for="image"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Product
                                Image</label>
                            <input type="file" id="image" name="image"
                                class="mt-1 block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-100 dark:file:bg-slate-950 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 dark:hover:file:bg-slate-900 border min-h-[42px] border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900"
                                accept="image/*" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <!-- Initial Stock Details -->
                        <div class="md:col-span-2 bg-slate-50 dark:bg-slate-800/50 p-6 rounded-[24px] border border-slate-100 dark:border-slate-700 mt-2">
                             <div class="flex items-center gap-2 mb-4">
                                <x-lucide-package class="w-4 h-4 text-brand-500" />
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Initial Stock Taking</h3>
                             </div>
                             
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="opening_stock" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Opening Quantity</label>
                                    <input id="opening_stock" type="number" name="opening_stock" value="{{ old('opening_stock', 0) }}"
                                        class="block w-full bg-white dark:bg-slate-900 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                                        placeholder="0" />
                                    <x-input-error :messages="$errors->get('opening_stock')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="opening_stock_unit_cost" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Opening Unit Cost ({{ $currency ?? '₹' }})</label>
                                    <input id="opening_stock_unit_cost" type="number" step="0.01" name="opening_stock_unit_cost" value="{{ old('opening_stock_unit_cost') }}"
                                        class="block w-full bg-white dark:bg-slate-900 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                                        placeholder="0.00" />
                                    <x-input-error :messages="$errors->get('opening_stock_unit_cost')" class="mt-2" />
                                </div>
                             </div>
                             <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-3 flex items-center gap-1">
                                <x-lucide-info class="w-3 h-3" />
                                This will set the starting stock level and create an initial history record.
                             </p>
                        </div>
                    </div>

                    <!-- Custom Specifications -->
                    <div class="md:col-span-2 mt-4" x-data="{
                        specs: [{ key: '', value: '' }],
                        addSpec() { this.specs.push({ key: '', value: '' }); },
                        removeSpec(index) { this.specs.splice(index, 1); }
                    }">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Product Specifications (Custom Fields)</label>
                            <button type="button" @click="addSpec" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 flex items-center gap-1">+ Add Field</button>
                        </div>
                        
                        <div class="space-y-3">
                            <template x-for="(spec, index) in specs" :key="index">
                                <div class="flex items-center gap-3">
                                    <input type="text" x-model="spec.key" :name="`specifications[${index}][key]`" placeholder="e.g. Color" class="flex-1 bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 rounded-xl shadow-sm text-sm" />
                                    <input type="text" x-model="spec.value" :name="`specifications[${index}][value]`" placeholder="e.g. Red" class="flex-1 bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 rounded-xl shadow-sm text-sm" />
                                    <button type="button" @click="removeSpec(index)" class="p-2 text-slate-400 hover:text-red-500 transition-colors" x-show="specs.length > 1"><x-lucide-trash-2 class="w-4 h-4" /></button>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">Add custom key-value pairs like color, weight, material, etc.</p>
                    </div>

                    <div>
                        <label for="description"
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Description</label>
                        <textarea id="description" name="description"
                            class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                            rows="4" placeholder="Describe this product type...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div
                        class="flex items-center justify-end border-t border-slate-100 dark:border-slate-800 pt-6 gap-3">
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 border-2 border-slate-200 dark:border-slate-700 text-sm font-semibold rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-800 h-[42px]">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-all focus:outline-none focus:ring-2 focus:ring-slate-900/20 dark:focus:ring-white/20 h-[42px]">
                            <x-lucide-save class="w-4 h-4" /></i> Save Product Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js logic for reactive category parsing -->
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('productForm', (categories) => ({
                    categories: categories,
                    categoryId: '{{ old("category_id") }}',
                    get selectedCategory() {
                        if (!this.categoryId) return null;
                        return this.categories.find(c => c.id === parseInt(this.categoryId));
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>