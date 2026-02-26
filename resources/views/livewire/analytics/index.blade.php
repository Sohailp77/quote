<div
    class="bg-[#f8fafc] dark:bg-slate-950 min-h-screen rounded-[40px] p-6 lg:p-10 font-sans text-slate-800 dark:text-slate-200 relative xl:overflow-hidden transition-colors duration-500">
    @php
        $currency = \App\Models\CompanySetting::getCurrencySymbol() ?? '₹';
        function fmt($value, $currency)
        {
            $n = floatval($value);
            if ($n >= 10000000)
                return $currency . number_format($n / 10000000, 1) . 'Cr';
            if ($n >= 100000)
                return $currency . number_format($n / 100000, 1) . 'L';
            if ($n >= 1000)
                return $currency . number_format($n / 1000, 1) . 'K';
            return $currency . number_format($n);
        }
    @endphp

    <!-- Background Orbs -->
    <div
        class="absolute top-[-5%] left-[-5%] w-[40%] h-[40%] bg-indigo-200/20 rounded-full blur-[120px] pointer-events-none">
    </div>
    <div
        class="absolute bottom-[-5%] right-[-5%] w-[30%] h-[30%] bg-emerald-200/20 rounded-full blur-[120px] pointer-events-none">
    </div>

    <div class="relative z-10">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="w-10 h-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-400 transition shadow-sm">
                    <x-lucide-arrow-left class="w-5 h-5" />
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Business Analytics</h1>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Performance, trends and
                        projections</p>
                </div>
            </div>
            <div
                class="flex items-center gap-2 bg-white dark:bg-slate-900 px-4 py-2 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                <div class="w-2 h-2 rounded-full {{ $stats['growth_rate'] >= 0 ? 'bg-emerald-500' : 'bg-red-500' }}">
                </div>
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">
                    {{ $stats['growth_rate'] >= 0 ? '+' : '' }}{{ $stats['growth_rate'] }}% this month
                </span>
            </div>
        </div>

        <!-- KPI Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-6 lg:p-8 rounded-[32px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-white/50 dark:border-slate-800 flex flex-col gap-4 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)]">
                <div
                    class="w-12 h-12 bg-brand-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/20">
                    <x-lucide-dollar-sign class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-2xl lg:text-3xl font-black text-slate-900 dark:text-white">
                        {{ fmt($stats['total_revenue'], $currency) }}</p>
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">
                        Total Revenue</p>
                </div>
            </div>

            <div
                class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-6 lg:p-8 rounded-[32px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-white/50 dark:border-slate-800 flex flex-col gap-4 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)]">
                <div
                    class="w-12 h-12 bg-red-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-500/20">
                    <x-lucide-arrow-down-right class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-2xl lg:text-3xl font-black text-slate-900 dark:text-white">
                        {{ fmt($stats['total_costs'], $currency) }}</p>
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">
                        Total Costs</p>
                </div>
            </div>

            <div
                class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-6 lg:p-8 rounded-[32px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-white/50 dark:border-slate-800 flex flex-col gap-4 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)]">
                <div
                    class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                    <x-lucide-arrow-up-right class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-2xl lg:text-3xl font-black text-slate-900 dark:text-white">
                        {{ fmt($stats['net_profit'], $currency) }}</p>
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">
                        Net Profit</p>
                </div>
            </div>

            <div
                class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-6 lg:p-8 rounded-[32px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-white/50 dark:border-slate-800 flex flex-col gap-4 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)]">
                <div
                    class="w-12 h-12 bg-sky-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-sky-500/20">
                    <x-lucide-trending-up class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-2xl lg:text-3xl font-black text-slate-900 dark:text-white">
                        {{ fmt($stats['projections']['next_month'], $currency) }}</p>
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">
                        Forecast (Mo)</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Monthly Trends -->
            <div
                class="lg:col-span-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-8 rounded-[40px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-white/50 dark:border-slate-800 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)]">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Revenue Trends</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest">Last 6
                            Months</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-slate-800 dark:text-slate-200">Avg:
                            {{ fmt($stats['projections']['avg_monthly'], $currency) }}</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest">
                            Per Month</p>
                    </div>
                </div>

                <div class="h-64 flex items-end gap-3">
                    @php
                        $maxRevVal = max($stats['monthly_revenue']->max('total') ?? 1, 1);
                        $reversedRevenue = collect($stats['monthly_revenue'])->reverse();
                    @endphp
                    @foreach($reversedRevenue as $data)
                        @php $hPct = max(8, ($data->total / $maxRevVal) * 100); @endphp
                        <div class="flex-1 flex flex-col items-center gap-4 h-full justify-end group">
                            <div
                                class="text-[10px] font-black text-slate-500 dark:text-slate-400 opacity-0 group-hover:opacity-100 transition translate-y-2 group-hover:translate-y-0 text-center">
                                {{ fmt($data->total, $currency) }}
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-950 rounded-2xl group-hover:bg-brand-500 transition-all duration-500"
                                style="height: {{ $hPct }}%;"></div>
                            <div
                                class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-tighter">
                                {{ \Carbon\Carbon::parse($data->month . '-01')->format('M y') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Products & Targets -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Best Sellers -->
                <div
                    class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[40px] p-8 text-white relative overflow-hidden shadow-[0_8px_30px_rgba(0,0,0,0.15)]">
                    <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                        <x-lucide-award class="w-20 h-20" />
                    </div>
                    <h3 class="text-base font-bold mb-6 flex items-center gap-2">
                        <x-lucide-briefcase class="w-4 h-4 text-brand-400" /> Best Sellers
                    </h3>
                    <div class="space-y-6 relative z-10">
                        @foreach($stats['top_products'] as $i => $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-white/10 rounded-xl flex items-center justify-center text-xs font-black">
                                        {{ $i + 1 }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold truncate max-w-[120px]">
                                            {{ optional($item->product)->name ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold">{{ $item->total_sold }} units sold
                                        </p>
                                    </div>
                                </div>
                                <p class="text-sm font-black text-brand-400 text-right">
                                    {{ fmt($item->revenue, $currency) }}
                                </p>
                            </div>
                        @endforeach
                        @if(count($stats['top_products']) === 0)
                            <p class="text-xs text-slate-400 italic">No products sold recently.</p>
                        @endif
                    </div>
                </div>

                <!-- Monthly Targets -->
                <div
                    class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-8 rounded-[40px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-white/50 dark:border-slate-800 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)]">
                    <h3
                        class="text-sm font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <x-lucide-target class="w-4 h-4 text-emerald-500 dark:text-emerald-400" /> Monthly
                        Targets
                    </h3>
                    <div class="space-y-6">
                        <!-- Revenue Goal -->
                        @php $revGoal = $stats['goals']['revenue']; @endphp
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <p
                                    class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest">
                                    Revenue</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ $revGoal['percent'] }}%
                                </p>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-950 rounded-full overflow-hidden">
                                <div class="h-full bg-brand-500 rounded-full transition-all duration-1000"
                                    style="width: {{ $revGoal['percent'] }}%;"></div>
                            </div>
                            <div
                                class="flex justify-between mt-1 text-[9px] font-bold text-slate-400 dark:text-slate-500">
                                <span>Current: {{ fmt($revGoal['current'], $currency) }}</span>
                                <span>Goal: {{ fmt($revGoal['target'], $currency) }}</span>
                            </div>
                        </div>

                        <!-- Stock Budget -->
                        @php $budGoal = $stats['goals']['budget']; @endphp
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <p
                                    class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest">
                                    Stock Spend</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ $budGoal['percent'] }}%
                                </p>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-950 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 {{ $budGoal['percent'] > 90 ? 'bg-red-500' : 'bg-sky-500' }}"
                                    style="width: {{ $budGoal['percent'] }}%;"></div>
                            </div>
                            <div
                                class="flex justify-between mt-1 text-[9px] font-bold text-slate-400 dark:text-slate-500">
                                <span>Spent: {{ fmt($budGoal['current'], $currency) }}</span>
                                <span>Limit: {{ fmt($budGoal['target'], $currency) }}</span>
                            </div>
                        </div>

                        <!-- Conversion Goal -->
                        @php $covGoal = $stats['goals']['conversion']; @endphp
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <p
                                    class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest">
                                    Conversion</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ $covGoal['percent'] }}%
                                </p>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-950 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000"
                                    style="width: {{ $covGoal['percent'] }}%;"></div>
                            </div>
                            <div
                                class="flex justify-between mt-1 text-[9px] font-bold text-slate-400 dark:text-slate-500">
                                <span>Avg: {{ $covGoal['current'] }}%</span>
                                <span>Goal: {{ $covGoal['target'] }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Teaser Section -->
        <div class="mt-12 mb-20">
            <div class="flex items-center justify-between gap-4 mb-8">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-slate-900 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center text-white dark:text-indigo-400 shadow-lg shadow-slate-900/20">
                            <x-lucide-activity class="w-5 h-5" />
                        </div>
                        Recent Activity
                    </h3>
                    <p
                        class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest mt-2 ml-[52px]">
                        Snapshot of latest business events</p>
                </div>

                <a href="{{ route('analytics.ledger') }}"
                    class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-white/50 dark:border-slate-800 rounded-2xl px-6 py-3 flex items-center gap-3 text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-sm transition-all group">
                    View Business Ledger
                    <x-lucide-chevron-right class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white transition-transform group-hover:translate-x-1" />
                </a>
            </div>

            <div
                class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-[40px] shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-white/50 dark:border-slate-800 overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)]">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800/50">
                        <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                            <tr>
                                @foreach(['Date', 'Type', 'Target Item', 'Description', 'Amount'] as $h)
                                    <th
                                        class="px-8 py-5 text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                                        {{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/20">
                            @if(count($recentLedger) === 0)
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <p class="text-slate-400 dark:text-slate-500 font-bold">No recent activity recorded.
                                        </p>
                                    </td>
                                </tr>
                            @else
                                @foreach($recentLedger as $entry)
                                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="text-sm font-black text-slate-700 dark:text-slate-300">
                                                {{ \Carbon\Carbon::parse($entry['date'])->format('j M Y, g:i A') }}</div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <span
                                                class="inline-flex px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-[0.15em] border {{ $entry['is_revenue'] ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800/50' : 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border-red-100 dark:border-red-800/50' }}">
                                                {{ $entry['type'] }}
                                            </span>
                                            @if($entry['reverted_at'])
                                                <span
                                                    class="ml-2 inline-flex px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-950 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700/50">
                                                    Reverted
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="text-sm font-black text-slate-700 dark:text-slate-300">
                                                {{ $entry['target_item'] }}</div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium max-w-[340px] truncate"
                                                title="{{ $entry['description'] }}">
                                                {{ $entry['description'] }}
                                            </p>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div
                                                class="text-base font-black {{ $entry['is_revenue'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-white ' . ($entry['reverted_at'] ? 'opacity-50 line-through' : '') }}">
                                                {{ $entry['is_revenue'] ? '+' : '-' }}{{ fmt($entry['amount'], $currency) }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>