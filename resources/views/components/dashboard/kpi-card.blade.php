@props(['label', 'value', 'sub' => null, 'subPositive' => null, 'icon', 'accent' => 'bg-brand-500'])

<div
    class="bg-white dark:bg-slate-900 rounded-[24px] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 hover:-translate-y-1 border border-slate-100/80 dark:border-slate-800/80 flex flex-col justify-between group relative overflow-hidden">
    <div
        class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-slate-50/50 dark:from-slate-800/50 to-transparent rounded-full opacity-50 -mr-10 -mt-10 pointer-events-none">
    </div>

    <div class="relative z-10 flex justify-between items-start mb-4">
        <div class="w-12 h-12 rounded-[16px] flex items-center justify-center {{ $accent }} shadow-inner">
            <x-dynamic-component :component="'lucide-' . ($icon)" class="w-5 h-5 text-white" />
        </div>

        @if($sub !== null)
            @php
                $subClass = '';
                if ($subPositive === true) {
                    $subClass = 'bg-emerald-50/30 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400';
                } elseif ($subPositive === false) {
                    $subClass = 'bg-rose-50/30 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400';
                } else {
                    $subClass = 'bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400';
                }
            @endphp
            <div class="px-2.5 py-1 rounded-full text-xs font-bold flex items-center gap-1 {{ $subClass }}">
                @if($subPositive === true)
                    <x-lucide-trending-up class="w-3 h-3" />
                @elseif($subPositive === false)
                    <x-lucide-trending-down class="w-3 h-3" />
                @endif
                {{ $sub }}
            </div>
        @endif
    </div>

    <div class="relative z-10">
        <p
            class="text-3xl font-black text-slate-900 dark:text-white tracking-tight group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
            {{ $value }}</p>
        <p class="text-[13px] font-semibold text-slate-500 dark:text-slate-400 mt-1">{{ $label }}</p>
    </div>
</div>