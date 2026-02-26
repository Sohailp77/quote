<x-app-layout>
    <div x-data="{ metricType: '{{ old('metric_type', 'area') }}' }">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('categories.index') }}"
                        class="p-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <x-lucide-arrow-left class="w-4 h-4" /></i>
                    </a>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">New Category</h1>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Define a new product line with its unit and metric
                    type.</p>
            </div>
        </div>

        <div class="max-w-2xl">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6 md:p-8">
                <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    <div>
                        <label for="name"
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Category
                            Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                            placeholder="e.g. Premium Tiles" autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="unit_name"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Unit
                                Name</label>
                            <input id="unit_name" type="text" name="unit_name" value="{{ old('unit_name') }}"
                                class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                                placeholder="e.g. Box, Bag, Pcs" />
                            <x-input-error :messages="$errors->get('unit_name')" class="mt-2" />
                        </div>

                        <div>
                            <label for="metric_type"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Metric
                                Type</label>
                            <select id="metric_type" name="metric_type" x-model="metricType"
                                class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm">
                                <option value="fixed">Fixed (Quantity only)</option>
                                <option value="area">Area (Sq.ft / Sq.m)</option>
                                <option value="weight">Weight (Kg / Tons)</option>
                            </select>
                            <x-input-error :messages="$errors->get('metric_type')" class="mt-2" />
                            <p class="mt-1 text-xs text-slate-400 dark:text-slate-500" x-text="
                                metricType === 'area' ? 'For items like Tiles (Coverage per Box)' :
                                (metricType === 'weight' ? 'For items like Cement (Weight per Bag)' :
                                'For standard items like Basins')
                            "></p>
                        </div>
                    </div>

                    <div>
                        <label for="image"
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Category Image
                            (Optional)</label>
                        <input type="file" id="image" name="image"
                            class="mt-1 block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-100 dark:file:bg-slate-950 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 dark:hover:file:bg-slate-900 border min-h-[42px] border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900"
                            accept="image/*" />
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>

                    <div>
                        <label for="description"
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Description</label>
                        <textarea id="description" name="description"
                            class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                            rows="4"
                            placeholder="Describe what this category contains...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div
                        class="flex items-center justify-end border-t border-slate-100 dark:border-slate-800 pt-6 gap-3">
                        <a href="{{ route('categories.index') }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 border-2 border-slate-200 dark:border-slate-700 text-sm font-semibold rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-800 h-[42px]">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-all focus:outline-none focus:ring-2 focus:ring-slate-900/20 dark:focus:ring-white/20 h-[42px]">
                            <x-lucide-save class="w-4 h-4" /></i> Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>