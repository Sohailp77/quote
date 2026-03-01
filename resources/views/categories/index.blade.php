<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Categories') }}
        </h2>
    </x-slot>

    <div class="py-6 lg:py-8">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10">

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show"
                    class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl relative flex justify-between items-center shadow-sm">
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Product Categories</h2>
                        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Define your product lines and their
                            metrics.</p>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <x-search-bar placeholder="Search categories..." />
                        <a href="{{ route('categories.create') }}"
                            class="inline-flex items-center justify-center gap-2 bg-slate-900 dark:bg-brand-500 text-white text-sm font-semibold px-5 py-2.5 rounded-2xl hover:bg-slate-700 dark:hover:bg-brand-600 transition-all shadow-sm h-[42px]">
                            <x-lucide-plus class="w-4 h-4" />
                            <span class="hidden sm:inline">Add Category</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($categories as $category)
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.06)] border border-slate-100 dark:border-slate-800 p-6 hover:shadow-[0_8px_24px_rgba(0,0,0,0.1)] transition-all hover:-translate-y-0.5 relative">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-950 flex items-center justify-center overflow-hidden">
                                @if ($category->image_path)
                                    <img src="{{ asset($category->image_path) }}" alt="{{ $category->name }}"
                                        class="h-full w-full object-cover">
                                @else
                                    <x-lucide-layers class="h-6 w-6 text-slate-400 dark:text-slate-500" />
                                @endif
                            </div>
                            <div class="flex gap-2 relative z-10">
                                <a href="{{ route('categories.edit', $category->id) }}"
                                    class="p-2 text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all">
                                    <x-lucide-edit-2 class="w-4 h-4" />
                                </a>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">{{ $category->name }}</h3>
                        <div class="flex items-center gap-2 mb-3 flex-wrap">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                Unit: {{ $category->unit_name }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800/50">
                                @if ($category->metric_type === 'area')
                                    <x-lucide-ruler class="w-3 h-3 text-purple-500" />
                                @elseif($category->metric_type === 'weight')
                                    <x-lucide-box class="w-3 h-3 text-brand-500 dark:text-brand-400" />
                                @else
                                    <x-lucide-check-circle class="w-3 h-3 text-green-500 dark:text-green-400" />
                                @endif
                                <span class="capitalize">{{ $category->metric_type }}</span>
                            </span>
                        </div>
                        <p class="text-sm text-slate-400 dark:text-slate-500">
                            {{ $category->description ?: 'No description provided.' }}</p>
                    </div>
                @empty
                    <div
                        class="col-span-full text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                        <x-lucide-layers class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" />
                        <h3 class="mt-3 text-sm font-semibold text-slate-700 dark:text-slate-300">No categories yet</h3>
                        <p class="mt-1 text-sm text-slate-400 dark:text-slate-500">Get started by creating a new product
                            category.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
