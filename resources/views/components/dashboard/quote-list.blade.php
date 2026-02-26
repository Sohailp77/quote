@props(['quotes', 'currency' => '₹', 'isBoss' => false])

@php
    $filters = ['all', 'accepted', 'sent', 'draft', 'rejected'];
    $bossStatuses = ['sent', 'accepted', 'rejected', 'expired'];
@endphp

<div x-data="{ filter: 'all', expandedId: null }">
    <!-- Filters -->
    <div class="flex gap-1.5 mb-4 flex-wrap">
        @foreach($filters as $f)
            <button @click="filter = '{{ $f }}'" class="px-3 py-1 rounded-full text-xs font-semibold capitalize transition"
                :class="filter === '{{ $f }}' ? 'bg-slate-900 dark:bg-brand-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'">
                @if($f === 'all')
                    All ({{ count($quotes) }})
                @else
                    {{ $f }}
                @endif
            </button>
        @endforeach
    </div>

    <!-- Quotes List -->
    <div class="space-y-2.5">
        @forelse($quotes->take(6) as $q)
            @php
                // Replicate STATUS util logic
                $st = match ($q->status) {
                    'draft' => ['bg' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400', 'label' => 'Draft', 'iconBg' => 'bg-slate-100 dark:bg-slate-800', 'iconColor' => 'text-slate-500 dark:text-slate-400'],
                    'sent' => ['bg' => 'bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-300', 'label' => 'Sent', 'iconBg' => 'bg-brand-100 dark:bg-brand-900', 'iconColor' => 'text-brand-600 dark:text-brand-400'],
                    'accepted' => ['bg' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-400', 'label' => 'Accepted', 'iconBg' => 'bg-emerald-100 dark:bg-emerald-900', 'iconColor' => 'text-emerald-600 dark:text-emerald-500'],
                    'rejected' => ['bg' => 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-400', 'label' => 'Rejected', 'iconBg' => 'bg-rose-100 dark:bg-rose-900', 'iconColor' => 'text-rose-600 dark:text-rose-500'],
                    'expired' => ['bg' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-400', 'label' => 'Expired', 'iconBg' => 'bg-orange-100 dark:bg-orange-900', 'iconColor' => 'text-orange-600 dark:text-orange-500'],
                    default => ['bg' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400', 'label' => 'Draft', 'iconBg' => 'bg-slate-100 dark:bg-slate-800', 'iconColor' => 'text-slate-500 dark:text-slate-400'],
                };
            @endphp
            <div x-show="filter === 'all' || filter === '{{ $q->status }}'"
                class="rounded-2xl border transition-[background,border-color,box-shadow] duration-200 overflow-hidden"
                :class="expandedId === {{ $q->id }} ? 'bg-slate-50/50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 shadow-sm' : 'bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700 hover:shadow-[0_4px_16px_rgba(0,0,0,0.03)] dark:hover:shadow-none'">

                <button @click="expandedId = expandedId === {{ $q->id }} ? null : {{ $q->id }}"
                    class="w-full flex items-center justify-between p-4 text-left">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors {{ $st['iconBg'] }} {{ $st['iconColor'] }}">
                            <x-lucide-file-text class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white leading-tight">
                                {{ $q->customer_name }}
                            </p>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $q->reference_id }} <span class="text-slate-300 dark:text-slate-600 mx-1">•</span>
                                {{ $q->created_at->diffForHumans() }}
                                @if($isBoss && $q->user)
                                    <span class="text-slate-300 dark:text-slate-600 mx-1">•</span>
                                    <span class="text-slate-600 dark:text-slate-400">{{ $q->user->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 flex-shrink-0">
                        <div class="text-right flex flex-col items-end">
                            <p class="text-[15px] font-black text-slate-900 dark:text-white">
                                {{ $currency }}{{ number_format($q->total_amount, 2) }}
                            </p>
                            <span
                                class="text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-md mt-1 {{ $st['bg'] }} dark:bg-opacity-20">{{ $st['label'] }}</span>
                        </div>
                        <div class="w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                            :class="expandedId === {{ $q->id }} ? 'bg-white dark:bg-slate-800 shadow-sm' : 'bg-slate-50/50 dark:bg-slate-800/50'">
                            <x-lucide-chevron-right
                                class="w-4 h-4 text-slate-400 dark:text-slate-500 transition-transform duration-300"
                                x-bind:class="expandedId === {{ $q->id }} ? 'rotate-90' : ''" />
                        </div>
                    </div>
                </button>

                <div x-show="expandedId === {{ $q->id }}" x-collapse.duration.300ms>
                    <div
                        class="px-4 pb-4 pt-2 bg-slate-50/50 dark:bg-slate-800/50 border-t border-slate-200/50/50 dark:border-slate-700/50">
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <a href="{{ route('quotes.pdf', $q->id) }}" target="_blank"
                                class="flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3.5 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                <x-lucide-printer class="w-3.5 h-3.5" /> View PDF
                            </a>

                            @if($isBoss)
                                @foreach($bossStatuses as $s)
                                    @if($s !== $q->status)
                                            <form action="{{ route('quotes.updateStatus', $q->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $s }}">
                                                <button type="submit"
                                                    class="text-xs font-bold px-3.5 py-2 rounded-xl border border-transparent shadow-sm transition-all capitalize flex items-center gap-1.5
                                                                                                            {{ $s === 'accepted' ? 'text-emerald-700 dark:text-emerald-400 bg-emerald-50/20 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 border-emerald-200/50/50 dark:border-emerald-800/50 hover:border-emerald-300 dark:hover:border-emerald-700'
                                        : ($s === 'rejected' ? 'text-rose-700 dark:text-rose-400 bg-rose-50/20 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 border-rose-200/50/50 dark:border-rose-800/50 hover:border-rose-300 dark:hover:border-rose-700'
                                            : 'text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border-slate-200 dark:border-slate-700 shadow-sm') }}">
                                                    <x-lucide-arrow-right class="w-3 h-3 opacity-50" /> Mark as {{ $s }}
                                                </button>
                                            </form>
                                    @endif
                                @endforeach
                            @elseif($q->status === 'draft')
                                <form action="{{ route('quotes.updateStatus', $q->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="sent">
                                    <button type="submit"
                                        class="flex items-center gap-1.5 text-xs font-bold text-brand-700 dark:text-brand-300 dark:text-brand-400 bg-brand-50/20 dark:bg-brand-900/20 border border-brand-200/50/50 dark:border-brand-800/50 px-3.5 py-2 rounded-xl hover:bg-brand-100 dark:hover:bg-brand-800 dark:hover:bg-brand-900/40 transition-all shadow-sm">
                                        <x-lucide-send class="w-3.5 h-3.5" /> Mark as Sent
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-slate-400 dark:text-slate-500">
                <x-lucide-file-text class="w-8 h-8 mx-auto mb-2 opacity-30" />
                <p class="text-sm">No quotes found here yet</p>
            </div>
        @endforelse
    </div>
</div>