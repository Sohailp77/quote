@props(['accepted' => 0, 'sent' => 0, 'draft' => 0, 'rejected' => 0, 'total' => 0])

@if($total == 0)
    <div
        class="flex items-center justify-center h-32 text-sm font-semibold text-slate-400 dark:text-slate-500 bg-slate-50/50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
        No quotes yet</div>
@else
    @php
        $segments = [
            ['pct' => ($accepted / $total) * 100, 'color' => '#10b981', 'label' => 'Accepted', 'val' => $accepted, 'bg' => 'bg-emerald-500', 'text' => 'text-emerald-700 dark:text-emerald-400'],
            ['pct' => ($sent / $total) * 100, 'color' => 'var(--color-brand-500, #6366f1)', 'label' => 'Pending', 'val' => $sent, 'bg' => 'bg-brand-500', 'text' => 'text-brand-700 dark:text-brand-300'],
            ['pct' => ($draft / $total) * 100, 'color' => '#94a3b8', 'label' => 'Draft', 'val' => $draft, 'bg' => 'bg-slate-400 dark:bg-slate-600', 'text' => 'text-slate-700 dark:text-slate-300'],
            ['pct' => ($rejected / $total) * 100, 'color' => '#f43f5e', 'label' => 'Rejected', 'val' => $rejected, 'bg' => 'bg-rose-500', 'text' => 'text-rose-700'],
        ];
        $cum = 0;
        $r = 48;
        $cx = 64;
        $cy = 64;
        $circ = 2 * pi() * $r;
    @endphp

    <div class="flex items-center gap-6">
        <div class="relative flex-shrink-0">
            <svg width="128" height="128" viewBox="0 0 128 128" class="drop-shadow-sm">
                @foreach($segments as $seg)
                    @php
                        $offset = $circ * (1 - $cum / 100);
                        $dash = $circ * ($seg['pct'] / 100);
                        $cum += $seg['pct'];
                    @endphp
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="{{ $seg['color'] }}"
                        stroke-width="12" stroke-dasharray="{{ $dash }} {{ $circ - $dash }}" stroke-dashoffset="{{ $offset }}"
                        stroke-linecap="round" transform="rotate(-90 64 64)" class="transition-all duration-1000 ease-out">
                    </circle>
                @endforeach
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center mt-0.5">
                <span class="text-2xl font-black text-slate-900 dark:text-white leading-none">{{ $total }}</span>
                <span
                    class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-1">Total</span>
            </div>
        </div>

        <div class="flex flex-col gap-3 flex-1">
            @foreach($segments as $seg)
                <div class="flex items-center justify-between group cursor-default">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-2.5 h-2.5 rounded-full {{ $seg['bg'] }} shadow-sm group-hover:scale-125 transition-transform">
                        </div>
                        <span
                            class="text-sm font-semibold text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-white transition-colors">{{ $seg['label'] }}</span>
                    </div>
                    <span class="text-sm font-bold {{ $seg['text'] }} dark:opacity-80">{{ $seg['val'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif