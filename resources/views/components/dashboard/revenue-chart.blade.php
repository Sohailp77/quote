@props(['dailyBars', 'currency' => '₹'])

@php
    $maxBar = max((is_array($dailyBars) && count($dailyBars) > 0) ? max($dailyBars) : 0, 1);
    $dayLabels = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

    $last7Labels = [];
    for ($i = 0; $i < 7; $i++) {
        $last7Labels[] = $dayLabels[now()->subDays(6 - $i)->dayOfWeek];
    }
@endphp

<div class="flex items-end justify-between h-40 gap-2 mt-4">
    @foreach($dailyBars as $i => $val)
        @php
            $hPct = max(8, ($val / $maxBar) * 100);
            $isToday = $i == 6;
        @endphp
        <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group cursor-crosshair relative">

            <!-- Tooltip -->
            <div
                class="absolute bottom-full mb-2 text-[11px] font-bold text-slate-700 dark:text-slate-200 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 bg-white dark:bg-slate-800 shadow-[0_4px_12px_rgb(0,0,0,0.08)] px-2.5 py-1 rounded-lg border border-slate-100 dark:border-slate-700 z-10 whitespace-nowrap">
                {{ $currency }}{{ number_format($val, 2) }}
            </div>

            <!-- Bar -->
            <div class="w-full flex justify-center items-end h-[calc(100%-24px)]">
                <div class="w-full max-w-[36px] rounded-t-xl transition-all duration-300 ease-out origin-bottom group-hover:scale-y-105 {{ $isToday ? 'bg-gradient-to-t from-brand-500 to-brand-400 shadow-[0_6px_20px_var(--color-brand-300)]' : ($val > 0 ? 'bg-slate-200 dark:bg-slate-700 group-hover:bg-brand-300 dark:group-hover:bg-brand-500' : 'bg-slate-100 dark:bg-slate-800') }}"
                    style="height: {{ $hPct }}%"></div>
            </div>

            <!-- Label -->
            <div
                class="text-xs font-bold transition-colors {{ $isToday ? 'text-brand-600 dark:text-brand-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300' }}">
                {{ $last7Labels[$i] }}
            </div>
        </div>
    @endforeach
</div>