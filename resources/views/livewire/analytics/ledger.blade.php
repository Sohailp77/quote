<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen text-slate-800 dark:text-slate-200 fade-in transition-colors duration-500">
    @php
        $currency = \App\Models\CompanySetting::getCurrencySymbol() ?? '₹';
        function fmt($value, $currency) {
            $n = floatval($value);
            return $currency . number_format($n, 2);
        }
    @endphp

    <div class="mb-10 animate-fade-in-up">
        <a href="{{ route('analytics.index') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] hover:text-slate-900 dark:hover:text-white transition-colors mb-6 group">
            <x-lucide-arrow-left class="w-3 h-3 transition-transform group-hover:-translate-x-1" /> Back to Analytics
        </a>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-900 dark:text-white flex items-center gap-4">
                    <div class="w-14 h-14 bg-slate-900 dark:bg-indigo-500/10 rounded-[22px] flex items-center justify-center text-white dark:text-indigo-400 shadow-xl shadow-slate-900/20">
                        <x-lucide-briefcase class="w-7 h-7" />
                    </div>
                    Business Ledger
                </h2>
                <p class="text-slate-400 dark:text-slate-500 font-bold mt-3 text-sm max-w-lg">
                    Deep audit trail of all financial events, stock movements, and revenue generations. 
                    <span class="text-slate-900 dark:text-white"> Precision is power.</span>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-white/50 dark:border-slate-800 rounded-2xl flex p-1 shadow-sm transition-all">
                    @foreach(['all' => 'All', 'revenue' => 'Revenue', 'cost' => 'Cost'] as $key => $label)
                        <button
                            wire:click="setFilterType('{{ $key }}')"
                            class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all duration-300 {{ $filterType === $key ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg shadow-slate-900/10' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-400' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Stats Bar -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="lg:col-span-3 relative group">
            <x-lucide-search class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 dark:text-slate-500 group-focus-within:text-slate-900 dark:group-focus-within:text-white transition-colors" />
            <input
                type="text"
                wire:model.live.debounce.300ms="searchQuery"
                placeholder="Search by product, ID, reference, or description..."
                class="w-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-2 border-white/50 dark:border-slate-800 dark:text-white rounded-3xl pl-14 pr-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:border-slate-900 dark:focus:border-slate-700 focus:ring-0 transition-all shadow-sm group-hover:shadow-md"
            />
            @if($searchQuery)
                <button
                    wire:click="$set('searchQuery', '')"
                    class="absolute right-6 top-1/2 -translate-y-1/2 p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl text-slate-400 dark:text-slate-500 transition"
                >
                    <x-lucide-x class="w-4 h-4" />
                </button>
            @endif
        </div>

        <button class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-white/50 dark:border-slate-800 rounded-3xl px-6 py-4 flex items-center justify-center gap-3 text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest hover:border-slate-300 dark:hover:border-slate-700 transition-all shadow-sm group">
            <x-lucide-download class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white" />
            Export Data
        </button>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-[44px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] border border-white/50 dark:border-slate-800 overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="overflow-x-auto relative">
            <div wire:loading.class="opacity-50 blur-sm pointer-events-none" class="transition-all duration-300">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800/50">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            @foreach(['Transaction ID', 'Status', 'Entity / Action', 'Details', 'Value'] as $h)
                                <th class="px-10 py-7 text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">{{ $h }}</th>
                            @endforeach
                            <th class="px-10 py-7 text-right text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                        @if($ledger->total() === 0)
                            <tr>
                                <td colspan="6" class="px-10 py-32 text-center text-slate-300 dark:text-slate-600">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800/50 rounded-[30px] flex items-center justify-center text-slate-200 dark:text-slate-700">
                                            <x-lucide-layout-grid class="w-10 h-10" />
                                        </div>
                                        <p class="font-bold text-slate-400 dark:text-slate-500">No transactions recorded for this filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach($ledger as $entry)
                                <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                                    <!-- Transaction ID -->
                                    <td class="px-10 py-8 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 group-hover:bg-slate-900 dark:group-hover:bg-slate-400 transition-colors"></div>
                                            <div>
                                                <div class="text-xs font-black text-slate-900 dark:text-white tabular-nums">#{{ 1000 + $entry['true_id'] }}</div>
                                                <div class="text-[10px] text-slate-400 dark:text-slate-500 font-bold mt-0.5" title="{{ \Carbon\Carbon::parse($entry['date'])->format('j M Y, h:i A') }}">
                                                    {{ \Carbon\Carbon::parse($entry['date'])->format('j M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="px-10 py-8 whitespace-nowrap">
                                        <span class="inline-flex px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] border {{ $entry['is_revenue'] ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800/50' : 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border-red-100 dark:border-red-800/50' }}">
                                            {{ $entry['type'] }}
                                        </span>
                                        @if($entry['reverted_at'])
                                            <span class="ml-2 inline-flex px-3 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700/50">
                                                Reverted
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <!-- Entity / Action -->
                                    <td class="px-10 py-8 whitespace-nowrap">
                                        <div class="text-sm font-black text-slate-900 dark:text-white">{{ $entry['target_item'] }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="w-4 h-4 bg-slate-100 dark:bg-slate-800 rounded-md flex items-center justify-center text-[8px] font-black text-slate-500 uppercase flex-shrink-0">
                                                <x-lucide-user class="w-2.5 h-2.5" />
                                            </div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-bold truncate max-w-[120px]">{{ $entry['user'] }}</div>
                                        </div>
                                    </td>
                                    
                                    <!-- Details -->
                                    <td class="px-10 py-8">
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium max-w-sm leading-relaxed truncate" title="{{ $entry['description'] }}">
                                            {{ $entry['description'] }}
                                        </p>
                                    </td>
                                    
                                    <!-- Value -->
                                    <td class="px-10 py-8 whitespace-nowrap">
                                        <div class="text-lg font-black {{ $entry['is_revenue'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-white' }} {{ $entry['reverted_at'] ? 'opacity-50 line-through' : '' }}">
                                            {{ $entry['is_revenue'] ? '+' : '-' }}{{ fmt($entry['amount'], $currency) }}
                                        </div>
                                        @if($entry['quantity'])
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-bold mt-1">
                                                {{ abs($entry['quantity']) }} units @ {{ fmt($entry['unit_cost'], $currency) }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-10 py-8 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
                                            @if(!$entry['reverted_at'] && !$entry['is_revenue'] && strtolower($entry['original_type']) !== 'reversion')
                                                <!-- Revert Button via POST route -->
                                                <form action="{{ route('stock.revert', $entry['true_id']) }}" method="POST" onsubmit="return confirm('Are you sure you want to revert this stock adjustment?');">
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="w-10 h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 hover:border-brand-200 hover:bg-brand-50 dark:hover:bg-brand-900/30 rounded-xl transition-all shadow-sm flex items-center justify-center p-0"
                                                        title="Revert Change"
                                                    >
                                                        <x-lucide-refresh-ccw class="w-4 h-4" />
                                                    </button>
                                                </form>
                                            @endif
                                            @if(!$entry['reverted_at'] && $entry['is_revenue'] && $entry['type'] === 'Manual Sale')
                                                <!-- Delete Button via DELETE route -->
                                                <form action="{{ route('revenues.destroy', $entry['true_id']) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this revenue record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="w-10 h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 hover:text-red-600 dark:hover:text-red-400 hover:border-red-200 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-all shadow-sm flex items-center justify-center p-0"
                                                        title="Delete Record"
                                                    >
                                                        <x-lucide-trash-2 class="w-4 h-4" />
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Loading overlay indicator -->
            <div wire:loading.flex class="absolute inset-0 items-center justify-center bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-10 transition-all opacity-0 pointer-events-none" style="opacity: 1; pointer-events: auto;" wire:loading.style="opacity: 1;">
                <div class="flex items-center gap-3 bg-white dark:bg-slate-800 px-6 py-4 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 font-bold text-slate-600 dark:text-slate-300">
                    <x-lucide-loader-2 class="w-5 h-5 animate-spin" />
                    <span>Filtering Ledger...</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="mt-8 animate-fade-in-up" style="animation-delay: 0.3s;">
        {{ $ledger->links() }}
    </div>

</div>
