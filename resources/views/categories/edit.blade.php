<x-app-layout>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('categories.index') }}"
                    class="p-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <x-lucide-arrow-left class="w-4 h-4" /></i>
                </a>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit: {{ $category->name }}</h1>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Update this category's name, unit or description.</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6 md:p-8">
            <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name"
                        class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Category
                        Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}"
                        class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm"
                        autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="unit_name"
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Unit
                            Name</label>
                        <input id="unit_name" type="text" name="unit_name"
                            value="{{ old('unit_name', $category->unit_name) }}"
                            class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm" />
                        <x-input-error :messages="$errors->get('unit_name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="metric_type"
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Metric
                            Type</label>
                        <select id="metric_type" name="metric_type"
                            class="mt-1 block w-full bg-slate-50 dark:bg-slate-800 border min-h-[42px] border-slate-200 dark:border-slate-700 focus:border-slate-400 dark:focus:border-slate-500 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-xl shadow-sm text-sm">
                            <option value="fixed" {{ old('metric_type', $category->metric_type) === 'fixed' ? 'selected' : '' }}>Fixed (Quantity only)</option>
                            <option value="area" {{ old('metric_type', $category->metric_type) === 'area' ? 'selected' : '' }}>Area (Sq.ft / Sq.m)</option>
                            <option value="weight" {{ old('metric_type', $category->metric_type) === 'weight' ? 'selected' : '' }}>Weight (Kg / Tons)</option>
                        </select>
                        <x-input-error :messages="$errors->get('metric_type')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <label for="image"
                        class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Update Image
                        (Optional)</label>
                    @if($category->image_path)
                        <div class="mb-3">
                            <img src="{{ $category->image_path }}" alt="Current"
                                class="w-16 h-16 object-cover rounded-xl shadow-sm" />
                        </div>
                    @endif
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
                        rows="4">{{ old('description', $category->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div
                    class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-6 gap-3">
                    <button type="button" onclick="document.getElementById('delete-form').submit();"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-sm font-semibold rounded-xl hover:bg-red-100 dark:hover:bg-red-900 transition-all focus:outline-none focus:ring-2 focus:ring-red-500/20 h-[42px]">
                        <x-lucide-trash-2 class="w-4 h-4" /></i> Delete Category
                    </button>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('categories.index') }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 border-2 border-slate-200 dark:border-slate-700 text-sm font-semibold rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-800 h-[42px]">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-all focus:outline-none focus:ring-2 focus:ring-slate-900/20 dark:focus:ring-white/20 h-[42px]">
                            <x-lucide-save class="w-4 h-4" /></i> Update Category
                        </button>
                    </div>
                </div>
            </form>

            <form id="delete-form" action="{{ route('categories.destroy', $category->id) }}" method="POST"
                class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-app-layout>