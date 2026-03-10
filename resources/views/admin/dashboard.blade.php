<x-app-layout>
    {{-- No standard header slot — we build our own inside the main area --}}

    <style>
        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 1.5rem;
            transition: box-shadow .2s, transform .2s;
        }
        .stat-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,.08); transform: translateY(-2px); }
        .dark .stat-card { background: #0f172a; border-color: #1e293b; }
        .glass-card {
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,.6);
            border-radius: 1.25rem;
        }
        .dark .glass-card {
            background: rgba(15,23,42,.85);
            border-color: rgba(255,255,255,.06);
        }
        .pulse-dot { animation: pulse-ring 2s cubic-bezier(.455,.03,.515,.955) infinite; }
        @keyframes pulse-ring { 0%,100%{opacity:1} 50%{opacity:.4} }
        .badge-secure { background: linear-gradient(135deg,#10b981,#059669); }
    </style>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-slate-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 py-8 space-y-8">

            {{-- ── HERO HEADER ──────────────────────────────────────────────── --}}
            <div class="relative overflow-hidden glass-card px-6 py-6 shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-transparent to-violet-600/5 pointer-events-none"></div>
                <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-violet-600 flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Command Center</h1>
                                <p class="text-xs text-slate-500 dark:text-slate-400">SuperAdmin Platform Management</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 pulse-dot"></span>
                            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">System Healthy</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Secure Session</span>
                        </div>
                        <a href="{{ route('admin.smtp.index') }}" class="px-4 py-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl shadow-md transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            SMTP Settings
                        </a>
                        <a href="{{ route('admin.tenants.create') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-violet-600 hover:from-blue-700 hover:to-violet-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            New Tenant
                        </a>
                    </div>
                </div>
            </div>

            {{-- ── KPI STATS ─────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Total Tenants --}}
                <div class="stat-card group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2.5 bg-blue-50 dark:bg-blue-950/60 rounded-xl group-hover:bg-blue-100 dark:group-hover:bg-blue-900/60 transition-colors">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <a href="{{ route('admin.tenants.index') }}" class="text-xs text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors font-medium">View all →</a>
                    </div>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $totalTenants }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total Tenants</p>
                    <div class="mt-3 flex items-center gap-1.5">
                        <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ $activeTenants }} active</span>
                        <span class="text-xs text-slate-400">·</span>
                        <span class="text-xs text-slate-400">{{ $totalTenants - $activeTenants }} suspended</span>
                    </div>
                </div>

                {{-- Total Users --}}
                <div class="stat-card group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2.5 bg-violet-50 dark:bg-violet-950/60 rounded-xl group-hover:bg-violet-100 dark:group-hover:bg-violet-900/60 transition-colors">
                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Across all tenants</span>
                    </div>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $totalUsers }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total Users</p>
                    <div class="mt-3">
                        <span class="text-xs text-slate-400">Avg {{ $totalTenants > 0 ? round($totalUsers / $totalTenants, 1) : 0 }} per tenant</span>
                    </div>
                </div>

                {{-- Active Plans --}}
                <div class="stat-card group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2.5 bg-amber-50 dark:bg-amber-950/60 rounded-xl group-hover:bg-amber-100 dark:group-hover:bg-amber-900/60 transition-colors">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <a href="{{ route('admin.plans.index') }}" class="text-xs text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors font-medium">Manage →</a>
                    </div>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $plansCount }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pricing Plans</p>
                    <div class="mt-3">
                        <span class="text-xs text-slate-400">Available subscription tiers</span>
                    </div>
                </div>

                {{-- 30-Day Growth --}}
                <div class="stat-card group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/60 rounded-xl group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/60 transition-colors">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Last 30 days</span>
                    </div>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white">+{{ $tenantsGrowth->sum() }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">New Tenants</p>
                    <div class="mt-3">
                        <span class="text-xs @if($tenantsGrowth->sum() > 0) text-emerald-600 dark:text-emerald-400 @else text-slate-400 @endif font-semibold">
                            @if($tenantsGrowth->sum() > 0) ↑ Growing @else → Steady @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── MAIN CONTENT GRID ─────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- Growth Chart (2/3 width) --}}
                <div class="xl:col-span-2 glass-card p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Tenant Acquisition</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">30-day new tenant trend</p>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-950/50 rounded-full border border-blue-100 dark:border-blue-900">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="text-xs text-blue-700 dark:text-blue-300 font-medium">New tenants / day</span>
                        </div>
                    </div>
                    <div id="growthChart" style="min-height:280px;"></div>
                </div>

                {{-- Plan Distribution (1/3 width) --}}
                <div class="glass-card p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Plan Mix</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tenants per plan</p>
                        </div>
                    </div>
                    <div id="planDonut" style="min-height:200px;"></div>
                    <div class="mt-4 space-y-2">
                        @foreach($planDistribution as $i => $p)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color:{{ ['#3b82f6','#8b5cf6','#f59e0b','#10b981','#ef4444'][$i % 5] }}"></span>
                                <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $p['name'] }}</span>
                            </div>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $p['count'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── RECENT TENANTS + ACTIVITY ────────────────────────────────── --}}
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

                {{-- Recent Tenants (3/5) --}}
                <div class="xl:col-span-3 glass-card shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Tenants</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Newest onboarded companies</p>
                        </div>
                        <a href="{{ route('admin.tenants.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentTenants as $t)
                        <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="w-9 h-9 flex-shrink-0 rounded-xl bg-gradient-to-br from-blue-400 to-violet-500 flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr($t->company_name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $t->company_name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-400">{{ $t->plan ? $t->plan->name : 'No Plan' }} · {{ $t->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $t->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400' }}">
                                    {{ $t->is_active ? 'Active' : 'Suspended' }}
                                </span>
                            </div>
                            <a href="{{ route('admin.tenants.edit', $t) }}" class="text-slate-300 dark:text-slate-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                        @empty
                        <div class="px-6 py-10 text-center text-sm text-slate-400">No tenants yet. <a href="{{ route('admin.tenants.create') }}" class="text-blue-600 hover:underline">Create one</a></div>
                        @endforelse
                    </div>
                </div>

                {{-- Activity Log (2/5) --}}
                <div class="xl:col-span-2 glass-card shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Access Log</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Last 20 events (max stored)</p>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-[400px] overflow-y-auto scrollbar-thin">
                        @forelse($recentActivity as $log)
                        <div class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <div class="mt-0.5 w-7 h-7 flex-shrink-0 rounded-lg flex items-center justify-center {{ str_contains($log->action, 'ACCESS') ? 'bg-blue-100 dark:bg-blue-950' : 'bg-amber-100 dark:bg-amber-950' }}">
                                <svg class="w-3.5 h-3.5 {{ str_contains($log->action, 'ACCESS') ? 'text-blue-600 dark:text-blue-400' : 'text-amber-600 dark:text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-tight">{{ $log->user?->name ?? 'Unknown' }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ $log->ip_address }}</p>
                            </div>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap flex-shrink-0 mt-0.5">{{ $log->created_at->diffForHumans(null, true) }}</span>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-xs text-slate-400">No activity recorded yet.</div>
                        @endforelse
                    </div>
                    {{-- Pagination --}}
                    @if($recentActivity->hasPages())
                    <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        @if($recentActivity->onFirstPage())
                            <span class="text-xs text-slate-300 dark:text-slate-600">← Prev</span>
                        @else
                            <a href="{{ $recentActivity->previousPageUrl() }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">← Prev</a>
                        @endif
                        <span class="text-xs text-slate-400">Page {{ $recentActivity->currentPage() }} / {{ $recentActivity->lastPage() }}</span>
                        @if($recentActivity->hasMorePages())
                            <a href="{{ $recentActivity->nextPageUrl() }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">Next →</a>
                        @else
                            <span class="text-xs text-slate-300 dark:text-slate-600">Next →</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── PLATFORM INTELLIGENCE ─────────────────────────────────────── --}}
            <div class="glass-card p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-xl">
                        <svg class="w-4 h-4 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Platform Intelligence</h3>
                        <p class="text-xs text-slate-400">Runtime & infrastructure diagnostics</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @php
                    $sysCards = [
                        ['label' => 'PHP Version', 'value' => $systemInfo['php_version'], 'icon' => '#10b981'],
                        ['label' => 'Laravel', 'value' => 'v' . $systemInfo['laravel_version'], 'icon' => '#ef4444'],
                        ['label' => 'Database', 'value' => $systemInfo['db_connection'], 'icon' => '#3b82f6'],
                        ['label' => 'Memory', 'value' => $systemInfo['memory_usage'], 'icon' => '#8b5cf6'],
                        ['label' => 'Server IP', 'value' => $systemInfo['server_ip'], 'icon' => '#f59e0b'],
                        ['label' => 'Status', 'value' => 'All Systems OK', 'icon' => '#10b981'],
                    ];
                    @endphp
                    @foreach($sysCards as $card)
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/50 hover:shadow-sm transition-shadow">
                        <div class="w-2 h-2 rounded-full mb-2" style="background-color:{{ $card['icon'] }}"></div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">{{ $card['label'] }}</p>
                        <p class="text-sm font-bold text-slate-900 dark:text-white mt-0.5 truncate">{{ $card['value'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? '#1e293b' : '#f1f5f9';
            const textColor = isDark ? '#64748b' : '#94a3b8';

            // Growth Chart
            new ApexCharts(document.querySelector('#growthChart'), {
                series: [{ name: 'New Tenants', data: @json($tenantsGrowth->values()) }],
                chart: { type: 'area', height: 280, toolbar: { show: false }, sparkline: { enabled: false } },
                stroke: { curve: 'smooth', width: 2.5 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 100] } },
                colors: ['#3b82f6'],
                xaxis: { categories: @json($tenantsGrowth->keys()), axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: textColor, fontSize: '11px' } } },
                yaxis: { labels: { style: { colors: textColor }, formatter: v => Math.round(v) } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                dataLabels: { enabled: false },
                tooltip: { theme: isDark ? 'dark' : 'light' },
            }).render();

            // Plan Donut
            const planData = @json($planDistribution);
            if (planData.length > 0) {
                new ApexCharts(document.querySelector('#planDonut'), {
                    series: planData.map(p => p.count || 0),
                    labels: planData.map(p => p.name),
                    chart: { type: 'donut', height: 200 },
                    colors: ['#3b82f6', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444'],
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Tenants', color: textColor, fontSize: '12px', fontWeight: 600 } } } } },
                    tooltip: { theme: isDark ? 'dark' : 'light' },
                    stroke: { width: 0 },
                }).render();
            } else {
                document.querySelector('#planDonut').innerHTML = '<div class="flex items-center justify-center h-[200px] text-sm text-slate-400">No plan data yet</div>';
            }
        });
    </script>
    @endpush
</x-app-layout>