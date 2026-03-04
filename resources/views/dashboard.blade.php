<x-app-layout>
    <div
        class="bg-[#f8fafc] dark:bg-slate-950 min-h-screen rounded-[40px] p-6 lg:p-10 font-sans text-slate-800 dark:text-slate-200 relative xl:overflow-hidden transition-colors duration-500">
        <!-- Background Orbs -->
        <div
            class="absolute top-[-5%] left-[-5%] w-[40%] h-[40%] bg-indigo-200/20 rounded-full blur-[120px] pointer-events-none">
        </div>
        <div
            class="absolute bottom-[-5%] right-[-5%] w-[30%] h-[30%] bg-emerald-200/20 rounded-full blur-[120px] pointer-events-none">
        </div>

        <div class="relative z-10" x-data="{ showCustomRange: {{ $timeframe === 'custom' ? 'true' : 'false' }} }">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-11 h-11 bg-gradient-to-br from-brand-600 to-brand-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/25">
                        @if ($userRole === 'boss')
                            <x-lucide-shield-check class="w-5 h-5" />
                        @else
                            <span class="font-black text-base">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white">
                            {{ $userRole === 'boss' ? 'Boss Dashboard' : 'My Dashboard' }}</h1>
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            {{ $userRole === 'boss' ? 'Full business overview' : 'Your personal performance overview' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <!-- Timeframe Filter Toggle -->
                    <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-full w-full sm:w-auto overflow-x-auto no-scrollbar">
                        @foreach(['weekly' => 'Week', 'monthly' => 'Month', 'yearly' => 'Year', 'all' => 'All'] as $key => $label)
                            <a href="{{ route('dashboard', ['timeframe' => $key]) }}"
                                class="px-4 py-1.5 text-xs font-semibold rounded-full whitespace-nowrap transition-colors {{ $timeframe === $key ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                        <button @click="showCustomRange = !showCustomRange"
                            class="px-4 py-1.5 text-xs font-semibold rounded-full whitespace-nowrap transition-colors {{ $timeframe === 'custom' ? 'bg-brand-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            Custom
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($userRole === 'boss')
                            <a href="{{ route('employees.index') }}"
                                class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-full text-sm font-semibold text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <x-lucide-users class="w-4 h-4" /> Team
                            </a>
                        @endif
                        <a href="{{ route('quotes.create') }}"
                            class="flex items-center gap-2 bg-slate-900 dark:bg-brand-500 text-white px-5 py-2.5 rounded-full text-sm font-bold shadow hover:bg-slate-700 dark:hover:bg-brand-600 transition">
                            <x-lucide-plus class="w-4 h-4" /> New
                        </a>
                    </div>
                </div>
            </div>

            <!-- Custom Range Picker -->
            <div x-show="showCustomRange" x-collapse style="display: none;">
                <div class="mb-8 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[32px] shadow-sm">
                    <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap items-end gap-4">
                        <input type="hidden" name="timeframe" value="custom">
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Date From</label>
                            <input type="date" name="start_date" value="{{ $quoteStats['start_date'] ?? '' }}" 
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Date To</label>
                            <input type="date" name="end_date" value="{{ $quoteStats['end_date'] ?? '' }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                        </div>
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition">Apply Range</button>
                    </form>
                </div>
            </div>

            @php
                $currency = \App\Models\CompanySetting::getCurrencySymbol() ?? '₹';
                $timeframeLabel = match($timeframe) {
                    'weekly' => 'this week',
                    'monthly' => 'this month',
                    'yearly' => 'this year',
                    'custom' => 'selected period',
                    'all' => 'all time',
                    default => 'this month'
                };
            @endphp

            <!-- KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <x-dashboard.kpi-card :label="$userRole === 'boss' ? 'Revenue (' . ucfirst($timeframe) . ')' : 'My Revenue (' . ucfirst($timeframe) . ')'" 
                    :value="$currency . number_format($quoteStats['filtered_revenue'] ?? 0, 2)" 
                    :sub="$timeframe === 'all' ? 'Liftime earnings' : 'For ' . $timeframeLabel" icon="coins" />

                @php
                    $growth = $quoteStats['growth'] ?? null;
                    $growthPositive = $growth !== null && $growth >= 0;
                    $subTextGrowth = $userRole === 'boss'
                            ? ($growth !== null
                                ? ($growthPositive ? '+' : '') . $growth . '% vs last month'
                                : 'vs. previous')
                            : 'Lifetime total';
                @endphp
                <x-dashboard.kpi-card label="Lifetime Revenue" :value="$currency . number_format($quoteStats['total_revenue'] ?? 0, 2)" 
                    :sub="$subTextGrowth" :subPositive="$userRole === 'boss' ? ($growth !== null ? $growthPositive : null) : null"
                    icon="trending-up" />

                <x-dashboard.kpi-card :label="$userRole === 'boss' ? 'Conversion Rate' : 'My Conversion'" :value="($quoteStats['conversion_rate'] ?? 0) . '%'" :sub="($quoteStats['accepted_count'] ?? 0) . ' accepted quotes'" :subPositive="($quoteStats['conversion_rate'] ?? 0) >= 50"
                    icon="target" />

                <x-dashboard.kpi-card :label="$userRole === 'boss' ? 'Avg Deal Size' : 'Quotes Generated'" :value="$userRole === 'boss' ? $currency . number_format($quoteStats['avg_deal_size'] ?? 0, 2) : ($quoteStats['total_quotes'] ?? 0)" :sub="$userRole === 'boss' ? 'During ' . $timeframeLabel : 'For ' . $timeframeLabel" :icon="$userRole === 'boss' ? 'bar-chart-3' : 'file-text'" />
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Left (7 cols) -->
                <div class="lg:col-span-7 flex flex-col gap-8">
                    <!-- Revenue Chart -->
                    <div
                        class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-white/50 dark:border-slate-800 rounded-[32px] p-7 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <x-lucide-trending-up class="w-4 h-4 text-brand-500 dark:text-brand-400" />
                                    {{ $userRole === 'boss' ? 'Revenue Trend' : 'My Revenue Trend' }}
                                </h2>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                    {{ $userRole === 'boss' ? 'Revenue breakdown' : 'Your earnings breakdown' }} for {{ $timeframeLabel }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-black text-slate-900 dark:text-white">
                                    {{ $currency }}{{ number_format(array_sum($quoteStats['daily_revenue'] ?? []), 2) }}
                                </div>
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $timeframeLabel }}</p>
                            </div>
                        </div>
                        <div>
                            <x-dashboard.revenue-chart :dailyBars="$quoteStats['daily_revenue'] ?? []" :labels="$quoteStats['chart_labels'] ?? []" :currency="$currency" />
                        </div>
                    </div>

                    <!-- Quote List (Paginated) -->
                    <div
                        class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-white/50 dark:border-slate-800 rounded-[32px] p-7 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $userRole === 'boss' ? 'Recent Quotes' : 'My Recent Quotes' }}</h3>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg">{{ $timeframeLabel }}</span>
                        </div>
                        <x-dashboard.quote-list :quotes="$quoteStats['recent_quotes']" :currency="$currency" :isBoss="$userRole === 'boss'" />
                        <!-- Pagination Links -->
                        <div class="mt-4 border-t border-slate-100 dark:border-slate-800/60 pt-4">
                            {{ $quoteStats['recent_quotes']->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>

                <!-- Right (5 cols) -->
                <div class="lg:col-span-5 flex flex-col gap-8">
                    <!-- Status Ring -->
                    <div
                        class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-white/50 dark:border-slate-800 rounded-[32px] p-7 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $userRole === 'boss' ? 'Quote Breakdown' : 'My Stats' }}</h3>
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg">
                                {{ $timeframeLabel }}</span>
                        </div>
                        <x-dashboard.status-ring :accepted="$quoteStats['accepted_count'] ?? 0" :sent="$quoteStats['sent_count'] ?? 0" :draft="$quoteStats['draft_count'] ?? 0"
                            :rejected="$quoteStats['rejected_count'] ?? 0" :total="$quoteStats['total_quotes'] ?? 0" />

                        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                            <div class="bg-slate-50/50 dark:bg-slate-800/50 rounded-xl p-3">
                                <p class="text-base font-black text-slate-900 dark:text-white">
                                    {{ $quoteStats['total_quotes'] ?? 0 }}</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">Period quotes</p>
                            </div>
                            <div class="bg-emerald-50/20 dark:bg-emerald-900/20 rounded-xl p-3">
                                <p class="text-base font-black text-emerald-700 dark:text-emerald-400">
                                    {{ $quoteStats['accepted_count'] ?? 0 }}</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                    {{ $userRole === 'boss' ? 'Accepted' : 'Won' }}</p>
                            </div>
                            <div class="bg-sky-50/20 dark:bg-sky-900/20 rounded-xl p-3">
                                <p class="text-base font-black text-sky-700 dark:text-sky-400">
                                    {{ $quoteStats['sent_count'] ?? 0 }}</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">Pending</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    @if ($userRole === 'boss')
                        <div
                            class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-white/50 dark:border-slate-800 rounded-[32px] p-7 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <x-lucide-award class="w-4 h-4 text-brand-500 dark:text-brand-400" /> Team Performance
                                </h3>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $timeframeLabel }}</span>
                            </div>
                            @if (count($employeePerformance ?? []) > 0)
                                <div class="space-y-3">
                                    @php $maxRev = max(1, $employeePerformance->max('quotes_sum_total_amount') ?? 1); @endphp
                                    @foreach ($employeePerformance as $i => $emp)
                                        @php $pct = max(8, (($emp->quotes_sum_total_amount ?? 0) / $maxRev) * 100); @endphp
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-black text-white {{ $i === 0 ? 'bg-brand-500' : 'bg-slate-300 dark:bg-slate-700' }}">{{ $i + 1 }}</span>
                                                    <span
                                                        class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $emp->name }}</span>
                                                </div>
                                                <div class="text-right">
                                                    <span
                                                        class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $currency }}{{ number_format($emp->quotes_sum_total_amount ?? 0, 2) }}</span>
                                                    <span
                                                        class="text-[10px] text-slate-400 dark:text-slate-500 ml-1">({{ $emp->quotes_count }})</span>
                                                </div>
                                            </div>
                                            <div
                                                class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $i === 0 ? 'bg-brand-400' : 'bg-slate-300 dark:bg-slate-600' }}"
                                                    style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6 text-slate-300 dark:text-slate-600 text-xs">
                                    <x-lucide-users class="w-8 h-8 mx-auto mb-2" />
                                    No performance data for this period.
                                </div>
                            @endif
                        </div>
                    @else
                        <div
                            class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-white/50 dark:border-slate-800 rounded-[32px] p-7 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">My Top Products</h3>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $timeframeLabel }}</span>
                            </div>
                            <div class="space-y-3">
                                @if (count($quoteStats['top_products'] ?? []) > 0)
                                    @php $maxCount = $quoteStats['top_products'][0]->quote_count ?? 1; @endphp
                                    @foreach (collect($quoteStats['top_products'])->take(4) as $i => $tp)
                                        @php $barPct = round(($tp->quote_count / $maxCount) * 100); @endphp
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="text-[10px] font-black w-5 h-5 rounded-md flex items-center justify-center text-white {{ $i === 0 ? 'bg-brand-500' : 'bg-slate-300 dark:bg-slate-700 !text-slate-600 dark:text-slate-300' }}">{{ $i + 1 }}</span>
                                                    <span
                                                        class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[140px]">{{ optional($tp->product)->name ?? 'Unknown' }}</span>
                                                </div>
                                                <span
                                                    class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $tp->quote_count }}×</span>
                                            </div>
                                            <div
                                                class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $i === 0 ? 'bg-brand-400' : 'bg-slate-300 dark:bg-slate-600' }}"
                                                    style="width: {{ $barPct }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-xs text-slate-400">No product stats for this period.</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($userRole === 'boss' && count($lowStockAlerts ?? []) > 0)
                        <div
                            class="bg-amber-50/80 dark:bg-amber-900/20 border border-amber-200/50 dark:border-amber-800/50 rounded-[32px] p-7 shadow-[0_8px_30px_rgb(0,0,0,0.02)]">
                            <h3
                                class="text-sm font-bold text-amber-800 dark:text-amber-500 flex items-center gap-2 mb-4">
                                <x-lucide-alert-triangle class="w-4 h-4" /> Low Stock Alerts
                            </h3>
                            <div class="space-y-2">
                                @foreach (collect($lowStockAlerts)->take(6) as $p)
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[150px]">
                                            {{ $p->name }}</p>
                                        <span
                                            class="text-xs font-black px-2 py-0.5 rounded-full {{ $p->stock_quantity == 0 ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400' }}">
                                            {{ $p->stock_quantity == 0 ? 'Out of Stock' : $p->stock_quantity . ' left' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div
                        class="bg-gradient-to-br from-brand-900 via-slate-900 to-slate-800 rounded-[32px] p-7 relative overflow-hidden shadow-[0_8px_30px_var(--color-brand-900)]">
                        <div class="absolute right-0 top-0 w-32 h-full opacity-10 flex"
                            style="background-image: radial-gradient(white 1.5px, transparent 1.5px); background-size: 12px 12px;">
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-1">
                                <x-lucide-zap class="w-4 h-4 text-brand-400" />
                                <h3 class="font-bold text-white text-sm">Quick Actions</h3>
                            </div>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mb-4">Jump to frequent tasks</p>
                            <div class="flex flex-col gap-2">
                                @php
                                    $quickActions = [
                                        [
                                            'href' => route('quotes.create'),
                                            'label' => $userRole === 'boss' ? 'New Quote' : 'Create New Quote',
                                            'icon' => 'file-text',
                                            'color' => 'text-brand-400',
                                        ],
                                    ];
                                    if ($userRole === 'boss') {
                                        $quickActions[] = [
                                            'href' => route('purchase-orders.index'),
                                            'label' => 'Stock Reorders',
                                            'icon' => 'truck',
                                            'color' => 'text-sky-400',
                                        ]; // purchase-orders.index
                                        $quickActions[] = [
                                            'href' => route('analytics.index'),
                                            'label' => 'Business Analytics',
                                            'icon' => 'bar-chart-3',
                                            'color' => 'text-emerald-400',
                                        ]; // analytics.index
                                        $quickActions[] = [
                                            'href' => route('settings.index'),
                                            'label' => 'Settings',
                                            'icon' => 'settings',
                                            'color' => 'text-slate-400 dark:text-slate-500',
                                        ];
                                    } else {
                                        $quickActions[] = [
                                            'href' => route('products.index'),
                                            'label' => 'Browse Products',
                                            'icon' => 'package',
                                            'color' => 'text-sky-400',
                                        ];
                                    }
                                @endphp
                                @foreach ($quickActions as $action)
                                    <a href="{{ $action['href'] }}"
                                        class="flex items-center justify-between bg-white/10 dark:bg-slate-950/30 hover:bg-white/20 dark:hover:bg-slate-950/50 text-white px-4 py-2.5 rounded-2xl text-sm font-semibold transition group">
                                        <div class="flex items-center gap-2">
                                            <x-dynamic-component :component="'lucide-' . $action['icon']"
                                                class="w-4 h-4 {{ $action['color'] }}" />
                                            {{ $action['label'] }}
                                        </div>
                                        <x-lucide-arrow-right
                                            class="w-4 h-4 opacity-40 group-hover:opacity-100 transition" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
