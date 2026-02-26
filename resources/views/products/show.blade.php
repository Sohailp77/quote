<x-app-layout>
    <x-slot name="header">
        <title>{{ $product->name }} - Details</title>
    </x-slot>

    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('products.index') }}" class="p-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <x-lucide-arrow-left class="w-4 h-4" /></i>
                </a>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $product->name }}</h1>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm">{{ $product->category->name ?? 'Product Details' }}</p>
        </div>
        <div>
            <a href="{{ route('products.edit', $product->id) }}" class="inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-950 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-semibold px-4 py-2.5 rounded-xl transition-all">
                <x-lucide-edit class="w-4 h-4" /></i> Edit Product
            </a>
        </div>
    </div>

    @php
        $baseStock = $product->stock_quantity ?? 0;
        $variantsStock = clone $product->variants;
        $variantsStockCount = $variantsStock->sum('stock_quantity') ?? 0;
        $totalStock = $baseStock + $variantsStockCount;
        $hasVariants = $product->variants->isNotEmpty();
        $isLow = $totalStock <= 5;
        $isOut = $totalStock === 0;
    @endphp

    <!-- Stock Panel Component (Livewire) -->
    <livewire:products.stock-panel :product="$product" :appSettings="$appSettings" :isBoss="$isBoss" />

    <!-- Product Summary Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] p-6 mb-5">
        <div class="flex flex-col md:flex-row justify-between items-start gap-6">
            <div class="flex items-start gap-5">
                @if($product->image_path)
                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-2xl shadow-sm flex-shrink-0" />
                @else
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-950 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <x-lucide-package class="w-8 h-8 text-slate-400 dark:text-slate-500" /></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">{{ $product->name }}</h1>
                    @if($product->category)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 text-xs font-medium mr-2">
                            <x-lucide-layers class="w-3 h-3" /></i> {{ $product->category->name }}
                        </span>
                    @endif
                    @if($product->sku)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-medium">
                            SKU: {{ $product->sku }}
                        </span>
                    @endif
                    <p class="text-sm text-slate-400 dark:text-slate-500 mt-3 max-w-lg">{{ $product->description ?: 'No description provided.' }}</p>
                </div>
            </div>
            <div class="text-right flex-shrink-0 w-full md:w-auto border-t md:border-t-0 border-slate-100 dark:border-slate-800 pt-4 md:pt-0">
                <div class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-widest font-semibold mb-1">Base Price</div>
                <div class="text-3xl font-bold text-slate-900 dark:text-white">{{ $appSettings['currency_symbol'] ?? '₹' }}{{ number_format($product->price, 2) }}</div>
                <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">per {{ $product->category->unit_name ?? 'unit' }}</div>
                @if($product->unit_size)
                    <div class="mt-3 inline-flex items-center gap-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2">
                        <x-lucide-package class="w-4 h-4 text-slate-400 dark:text-slate-500" /></i>
                        <div class="text-left">
                            <span class="block text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                {{ optional($product->category)->metric_type === 'area' ? 'Coverage' : 'Unit Size' }}
                            </span>
                            <span class="text-sm font-semibold text-slate-900 dark:text-white">
                                {{ $product->unit_size }} {{ optional($product->category)->metric_type === 'area' ? 'sq.m' : '' }} / {{ optional($product->category)->unit_name }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Variants Section (Livewire) -->
    <livewire:products.variant-manager :product="$product" :appSettings="$appSettings" />

</x-app-layout>
