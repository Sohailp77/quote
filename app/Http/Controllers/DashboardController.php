<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tenant = $user->tenant()->with('plan')->first();
        $plan = $tenant?->plan;

        // Superadmins should use the admin panel, not the tenant dashboard
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $timeframe = $request->query('timeframe', 'monthly');
        $validTimeframes = ['weekly', 'monthly', 'yearly', 'all', 'custom'];
        if (!in_array($timeframe, $validTimeframes)) {
            $timeframe = 'monthly';
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $start = null;
        $end = null;

        $user = auth()->user();
        $isBoss = $user->isBoss();
        $userRole = $isBoss ? 'boss' : 'employee';

        $quotesQuery = Quote::query();
        if (!$isBoss) {
            $quotesQuery->where('user_id', $user->id);
        }

        // Lifetime stats (for comparison)
        $lifetimeRevenue = (clone $quotesQuery)->where('status', 'accepted')->sum('total_amount');

        // Apply Timeframe filter
        $filteredQuotesQuery = clone $quotesQuery;
        if ($timeframe !== 'all') {
            if ($timeframe === 'custom' && $startDate && $endDate) {
                try {
                    $start = \Carbon\Carbon::parse($startDate)->startOfDay();
                    $end = \Carbon\Carbon::parse($endDate)->endOfDay();
                } catch (\Exception $e) {
                    $timeframe = 'monthly';
                }
            }

            if (!$start || !$end) {
                $start = match ($timeframe) {
                    'weekly' => now()->startOfWeek(),
                    'monthly' => now()->startOfMonth(),
                    'yearly' => now()->startOfYear(),
                    default => now()->startOfMonth(),
                };
                $end = match ($timeframe) {
                    'weekly' => now()->endOfWeek(),
                    'monthly' => now()->endOfMonth(),
                    'yearly' => now()->endOfYear(),
                    default => now()->endOfMonth(),
                };
            }

            // We filter by DIFFERENT columns depending on what we are counting
            // For general counts/lists, we use created_at.
            // For revenue, we will use filtered logic below.
            $filteredQuotesQuery->whereBetween('created_at', [$start, $end]);
        }

        // Period KPIs
        $totalQuotes = (clone $filteredQuotesQuery)->count();
        
        // Revenue should be filtered by ACCEPTED_AT for the chosen period
        $revenueQuery = (clone $quotesQuery)->where('status', 'accepted');
        if ($timeframe !== 'all') {
            $revenueQuery->whereBetween('accepted_at', [$start, $end]);
        }
        
        $acceptedStats = $revenueQuery
            ->selectRaw('SUM(total_amount) as revenue, SUM(total_cost) as cost')
            ->first();

        $acceptedRevenue = $acceptedStats->revenue ?? 0;
        $acceptedCost = $acceptedStats->cost ?? 0;
        $netProfit = $acceptedRevenue - $acceptedCost;

        $avgDealSize = $totalQuotes > 0 ? $acceptedRevenue / $totalQuotes : 0;

        $statusBreakdown = (clone $filteredQuotesQuery)->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $acceptedCount = $statusBreakdown['accepted'] ?? 0;
        $sentCount = $statusBreakdown['sent'] ?? 0;
        $draftCount = $statusBreakdown['draft'] ?? 0;
        $rejectedCount = $statusBreakdown['rejected'] ?? 0;
        $expiredCount = $statusBreakdown['expired'] ?? 0;
        $conversionRate = $totalQuotes > 0 ? round(($acceptedCount / $totalQuotes) * 100, 1) : 0;

        $growth = null;
        if ($timeframe === 'monthly' && $isBoss) {
            $lastMonthRevenue = (clone $quotesQuery)->where('status', 'accepted')->whereBetween('accepted_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])->sum('total_amount');
            $growth = $lastMonthRevenue > 0 ? round((($acceptedRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : null;
        }

        // Chart Data logic using efficient grouping
        if ($timeframe === 'weekly' || ($timeframe === 'custom' && isset($start) && $start->diffInDays($end) <= 7)) {
            $days = $timeframe === 'weekly' ? 7 : $start->diffInDays($end) + 1;
            $currentStart = $timeframe === 'weekly' ? now()->subDays(6)->startOfDay() : $start;
            $currentEnd = $timeframe === 'weekly' ? now()->endOfDay() : $end;

            $dailyData = (clone $quotesQuery)->where('status', 'accepted')
                ->whereBetween('accepted_at', [$currentStart, $currentEnd])
                ->selectRaw('DATE(accepted_at) as date, SUM(total_amount) as total')
                ->groupBy('date')
                ->pluck('total', 'date');

            for ($i = 0; $i < $days; $i++) {
                $date = $currentStart->copy()->addDays($i);
                $dateString = $date->toDateString();
                $chartLabels[] = $date->format('D');
                $chartData[] = $dailyData[$dateString] ?? 0;
            }
        } elseif ($timeframe === 'monthly' || ($timeframe === 'custom' && isset($start) && $start->diffInDays($end) <= 31)) {
            $days = $timeframe === 'monthly' ? now()->daysInMonth : $start->diffInDays($end) + 1;
            $currentStart = $timeframe === 'monthly' ? now()->startOfMonth() : $start;
            $currentEnd = $timeframe === 'monthly' ? now()->endOfMonth() : $end;

            $dailyData = (clone $quotesQuery)->where('status', 'accepted')
                ->whereBetween('accepted_at', [$currentStart, $currentEnd])
                ->selectRaw('DATE(accepted_at) as date, SUM(total_amount) as total')
                ->groupBy('date')
                ->pluck('total', 'date');

            for ($i = 0; $i < $days; $i++) {
                $date = $currentStart->copy()->addDays($i);
                $dateString = $date->toDateString();
                $chartLabels[] = $date->format('j');
                $chartData[] = $dailyData[$dateString] ?? 0;
            }
        } elseif ($timeframe === 'yearly') {
            $currentYear = now()->year;
            $monthlyData = (clone $quotesQuery)->where('status', 'accepted')
                ->whereYear('accepted_at', $currentYear)
                ->selectRaw('EXTRACT(MONTH FROM accepted_at) as month, SUM(total_amount) as total')
                ->groupBy('month')
                ->pluck('total', 'month');

            for ($i = 1; $i <= 12; $i++) {
                $chartLabels[] = \Carbon\Carbon::create()->month($i)->format('M');
                $chartData[] = $monthlyData[(float)$i] ?? $monthlyData[$i] ?? 0;
            }
        } else {
            $currentStart = now()->subDays(6)->startOfDay();
            $currentEnd = now()->endOfDay();

            $dailyData = (clone $quotesQuery)->where('status', 'accepted')
                ->whereBetween('accepted_at', [$currentStart, $currentEnd])
                ->selectRaw('DATE(accepted_at) as date, SUM(total_amount) as total')
                ->groupBy('date')
                ->pluck('total', 'date');

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateString = $date->toDateString();
                $chartLabels[] = $date->format('D');
                $chartData[] = $dailyData[$dateString] ?? 0;
            }
        }

        // Paginated Recent Quotes (Filtered by timeframe)
        $recentQuotes = $filteredQuotesQuery->with('user:id,name')->latest()->paginate(5);
        $recentQuotes->appends(['timeframe' => $timeframe, 'start_date' => $startDate, 'end_date' => $endDate]);

        // Top Products (Filtered by timeframe)
        $topProductsQuery = QuoteItem::select('product_id', DB::raw('COUNT(*) as quote_count'), DB::raw('SUM(quantity) as total_qty'))
            ->join('quotes', 'quote_items.quote_id', '=', 'quotes.id')
            ->where('quotes.status', 'accepted')
            ->with('product:id,name,image_path,stock_quantity')
            ->groupBy('product_id')->orderByDesc('quote_count')->take(5);

        if ($timeframe !== 'all') {
            $topProductsQuery->whereBetween('quotes.created_at', [$start, $end]);
        }

        if (!$isBoss) {
            $topProductsQuery->where('quotes.user_id', $user->id);
        }
        $topProducts = $topProductsQuery->get();

        // Boss Specific Data
        $employeePerformance = collect();
        $lowStockAlerts = collect();

        if ($isBoss) {
            $empQuery = User::where('role', 'employee')
                ->withCount([
                    'quotes' => function ($q) use ($timeframe, $start, $end) {
                        if ($timeframe !== 'all') {
                            $q->whereBetween('created_at', [$start, $end]);
                        }
                    }
                ])
                ->withSum([
                    'quotes' => function ($q) use ($timeframe, $start, $end) {
                        if ($timeframe !== 'all') {
                            $q->whereBetween('created_at', [$start, $end]);
                        }
                        $q->where('status', 'accepted');
                    }
                ], 'total_amount')
                ->orderByDesc('quotes_sum_total_amount');

            $employeePerformance = $empQuery->get(['id', 'name', 'email']);

            $lowStockProducts = Product::where('stock_quantity', '<=', 5)
                ->select('id', 'name', 'stock_quantity', 'sku')
                ->get()
                ->map(function ($item) {
                    $item->is_variant = false;
                    return $item;
                });

            $lowStockVariants = ProductVariant::where('stock_quantity', '<=', 5)
                ->with('product:id,name')
                ->get()
                ->map(function ($item) {
                    $item->name = $item->product ? $item->product->name . ' (' . $item->name . ')' : $item->name;
                    $item->is_variant = true;
                    return (object) [
                        'id' => $item->id,
                        'name' => $item->name,
                        'stock_quantity' => $item->stock_quantity,
                        'sku' => $item->sku,
                        'is_variant' => true,
                    ];
                });

            $lowStockAlerts = $lowStockProducts->concat($lowStockVariants)
                ->sortBy('stock_quantity')
                ->take(8)
                ->values();
        }

        $stats = [
            'total_categories' => Category::count(),
            'total_products' => Product::count(),
        ];

        $lifetimeStats = (clone $quotesQuery)->where('status', 'accepted')
            ->selectRaw('SUM(total_amount) as revenue, SUM(total_cost) as cost')
            ->first();

        $quoteStats = [
            'total_quotes' => $totalQuotes,
            'total_revenue' => $lifetimeStats->revenue ?? 0,
            'lifetime_profit' => ($lifetimeStats->revenue ?? 0) - ($lifetimeStats->cost ?? 0),
            'filtered_revenue' => $acceptedRevenue,
            'accepted_cost' => $acceptedCost,
            'net_profit' => $netProfit,
            'avg_deal_size' => $avgDealSize,
            'conversion_rate' => $conversionRate,
            'accepted_count' => $acceptedCount,
            'sent_count' => $sentCount,
            'draft_count' => $draftCount,
            'rejected_count' => $rejectedCount,
            'expired_count' => $expiredCount,
            'recent_quotes' => $recentQuotes,
            'top_products' => $topProducts,
            'daily_revenue' => $chartData,
            'chart_labels' => $chartLabels,
            'growth' => $growth,
            'start_date' => isset($start) ? $start->toDateString() : null,
            'end_date' => isset($end) ? $end->toDateString() : null,
        ];

        return view('dashboard', compact(
            'timeframe',
            'userRole',
            'stats',
            'quoteStats',
            'employeePerformance',
            'lowStockAlerts',
            'plan'
        ));
    }
}
