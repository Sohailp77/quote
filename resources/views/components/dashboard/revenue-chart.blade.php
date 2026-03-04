@props(['dailyBars', 'labels' => [], 'currency' => '₹'])

@php
    $maxBar = max((is_array($dailyBars) && count($dailyBars) > 0) ? max($dailyBars) : 0, 1);
    
    // Fallback labels if none provided (last 7 days)
    if (empty($labels)) {
        $dayLabels = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
        for ($i = 0; $i < 7; $i++) {
            $labels[] = $dayLabels[now()->subDays(6 - $i)->dayOfWeek];
        }
    }
@endphp

<div class="flex items-end justify-between h-40 pt-10 mt-4 no-scrollbar pb-2">
    @foreach($dailyBars as $i => $val)
        @php
            $hPct = max(8, ($val / $maxBar) * 100);
            $isLast = $i == (count($dailyBars) - 1);
            $label = $labels[$i] ?? '';
        @endphp
        <div class="flex-1 min-w-[20px] flex flex-col items-center gap-2 h-full justify-end group cursor-crosshair relative">

            <!-- Tooltip -->
            <div
                class="absolute bottom-full mb-2 text-[10px] font-bold text-slate-700 dark:text-slate-200 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 bg-white dark:bg-slate-800 shadow-[0_4px_12px_rgb(0,0,0,0.1)] px-2 py-1 rounded-lg border border-slate-100 dark:border-slate-700 z-10 whitespace-nowrap">
                {{ $currency }}{{ number_format($val, 2) }}
            </div>

            <!-- Bar -->
            <div class="w-full flex justify-center items-end h-[calc(100%-24px)]">
                <div class="w-full max-w-[28px] rounded-t-lg transition-all duration-300 ease-out origin-bottom group-hover:scale-y-105 {{ $isLast ? 'bg-gradient-to-t from-brand-500 to-brand-400 shadow-[0_4px_12px_var(--color-brand-400)]' : ($val > 0 ? 'bg-slate-200 dark:bg-slate-700 group-hover:bg-brand-300 dark:group-hover:bg-brand-500' : 'bg-slate-100 dark:bg-slate-800') }}"
                    style="height: {{ $hPct }}%"></div>
            </div>

            <!-- Label -->
            <div
                class="text-[9px] font-bold transition-colors {{ $isLast ? 'text-brand-600 dark:text-brand-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-300' }}">
                {{ $label }}
            </div>
        </div>
    @endforeach
</div>



{{-- @props(['dailyBars', 'labels' => [], 'currency' => '₹'])

@php
    $maxBar = max((is_array($dailyBars) && count($dailyBars) > 0) ? max($dailyBars) : 0, 1);
    
    if (empty($labels)) {
        $labels = array_fill(0, count($dailyBars), '');
    }
@endphp

<div class="flex items-end h-40 pt-10 gap-2 mt-4 overflow-x-auto no-scrollbar pb-2">
    @foreach($dailyBars as $i => $val)
        @php
            $hPct = max(8, ($val / $maxBar) * 100);
            $isLast = $i == (count($dailyBars) - 1);
            $label = $labels[$i] ?? '';
        @endphp
        
        <div class="flex-none w-8 flex flex-col items-center justify-end h-full group cursor-crosshair relative">

            <div class="absolute bottom-full mb-2 text-[10px] font-bold text-slate-700 dark:text-slate-200 opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none transform translate-y-2 group-hover:translate-y-0 bg-white dark:bg-slate-800 shadow-lg px-2 py-1 rounded-lg border border-slate-100 dark:border-slate-700 z-20 whitespace-nowrap">
                {{ $currency }}{{ number_format($val, 2) }}
            </div>

            <div class="w-full flex-grow flex items-end justify-center">
                <div class="w-full max-w-[20px] rounded-t-lg transition-all duration-300 ease-out origin-bottom group-hover:scale-y-105 {{ $isLast ? 'bg-gradient-to-t from-brand-500 to-brand-400 shadow-[0_4px_12px_rgba(var(--brand-primary-rgb),0.3)]' : ($val > 0 ? 'bg-slate-200 dark:bg-slate-700 group-hover:bg-brand-300' : 'bg-slate-100 dark:bg-slate-800') }}"
                    style="height: {{ $hPct }}%"></div>
            </div>

            <div class="h-6 flex items-center justify-center text-[9px] font-bold {{ $isLast ? 'text-brand-600 dark:text-brand-400' : 'text-slate-400 dark:text-slate-500' }}">
                {{ $label }}
            </div>
        </div>
    @endforeach
</div> --}}