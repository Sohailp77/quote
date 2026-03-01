<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Products') }}
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
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Product Types</h2>
                        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Manage your product catalog (e.g.
                            60×60 Vitrified, Matt).</p>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <x-search-bar placeholder="Search products..." />
                        <a href="{{ route('products.create') }}"
                            class="inline-flex items-center justify-center gap-2 bg-slate-900 dark:bg-brand-500 text-white text-sm font-semibold px-5 py-2.5 rounded-2xl hover:bg-slate-700 dark:hover:bg-brand-600 transition-all shadow-sm h-[42px]">
                            <x-lucide-plus class="w-4 h-4" />
                            <span class="hidden sm:inline">Add Product</span>
                        </a>
                    </div>
                </div>
            </div>

            @if ($products->count() === 0)
                <div
                    class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                    <x-lucide-package class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" />
                    <h3 class="mt-3 text-sm font-semibold text-slate-700 dark:text-slate-300">No product types</h3>
                    <p class="mt-1 text-sm text-slate-400 dark:text-slate-500">Get started by creating a new product
                        type (e.g. 60x60 Vitrified).</p>
                    <div class="mt-5">
                        <a href="{{ route('products.create') }}"
                            class="inline-flex items-center gap-2 bg-slate-900 dark:bg-brand-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-slate-700 dark:hover:bg-brand-600 transition-all">
                            <x-lucide-plus class="w-4 h-4" /> New Product Type
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @php
                        $currency = \App\Models\CompanySetting::where('key', 'currency_symbol')->value('value') ?? '₹';
                    @endphp
                    @foreach ($products as $product)
                        <div
                            class="group bg-white dark:bg-slate-900 rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.06)] border border-slate-100 dark:border-slate-800 overflow-hidden hover:shadow-[0_8px_24px_rgba(0,0,0,0.1)] transition-all hover:-translate-y-0.5 flex flex-col">
                            <div class="bg-slate-100 dark:bg-slate-950 relative overflow-hidden">
                                @if ($product->image_path)
                                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}"
                                        class="object-cover w-full h-44 group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="flex items-center justify-center h-44">
                                        <x-lucide-package class="w-10 h-10 text-slate-300 dark:text-slate-600" />
                                    </div>
                                @endif
                                <div
                                    class="absolute top-3 right-3 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-800 dark:text-slate-200 shadow-sm border border-slate-200/50 dark:border-slate-700/50">
                                    {{ $currency }}{{ number_format($product->price, 2) }}
                                </div>
                            </div>
                            <div class="p-5 flex flex-col flex-1">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="pr-2">
                                        <a href="{{ route('products.show', $product->id) }}"
                                            class="text-base font-bold text-slate-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400 transition-colors line-clamp-1 block">
                                            {{ $product->name }}
                                        </a>
                                        @if ($product->category)
                                            <div
                                                class="text-xs text-slate-400 dark:text-slate-500 flex items-center mt-0.5">
                                                <x-lucide-layers class="w-3 h-3 mr-1" />{{ $product->category->name }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex gap-1 flex-shrink-0">
                                        <a href="{{ route('products.edit', $product->id) }}"
                                            class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-all">
                                            <x-lucide-edit-2 class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <p class="text-sm text-slate-400 dark:text-slate-500 mb-4 line-clamp-2 min-h-[2.5rem]">
                                    {{ $product->description ?: 'No description.' }}</p>

                                <div
                                    class="mt-auto border-t border-slate-100 dark:border-slate-800 pt-3 flex justify-between items-center text-sm">
                                    <div>
                                        <span class="block text-slate-400 dark:text-slate-500 text-xs text-left">Base
                                            Price</span>
                                        <span
                                            class="font-semibold text-slate-900 dark:text-white">{{ $currency }}{{ number_format($product->price, 2) }}</span>
                                    </div>
                                    @if ($product->unit_size)
                                        <div class="text-right">
                                            <span
                                                class="block text-slate-400 dark:text-slate-500 text-xs">{{ optional($product->category)->metric_type === 'area' ? 'Coverage' : 'Unit Size' }}</span>
                                            <span
                                                class="font-semibold text-slate-900 dark:text-white">{{ $product->unit_size }}
                                                <span
                                                    class="text-xs text-slate-400 dark:text-slate-500">{{ optional($product->category)->unit_name }}</span></span>
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ route('products.show', $product->id) }}"
                                    class="mt-4 w-full text-center py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-sm transition-colors border border-transparent dark:border-slate-700">
                                    Manage Variants
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
