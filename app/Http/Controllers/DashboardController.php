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

            $filteredQuotesQuery->whereBetween('created_at', [$start, $end]);
        }

        // Period KPIs
        $totalQuotes = (clone $filteredQuotesQuery)->count();
        $acceptedRevenue = (clone $filteredQuotesQuery)->where('status', 'accepted')->sum('total_amount');
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
            $lastMonthRevenue = (clone $quotesQuery)->where('status', 'accepted')->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])->sum('total_amount');
            $growth = $lastMonthRevenue > 0 ? round((($acceptedRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : null;
        }

        // Chart Data logic
        $chartData = [];
        $chartLabels = [];

        if ($timeframe === 'weekly' || ($timeframe === 'custom' && isset($start) && $start->diffInDays($end) <= 7)) {
            $days = $timeframe === 'weekly' ? 7 : $start->diffInDays($end) + 1;
            $current = $timeframe === 'weekly' ? now()->subDays(6) : $start;
            for ($i = 0; $i < $days; $i++) {
                $date = $current->copy()->addDays($i)->toDateString();
                $chartLabels[] = $current->copy()->addDays($i)->format('D');
                $chartData[] = (clone $quotesQuery)->where('status', 'accepted')->whereDate('created_at', $date)->sum('total_amount');
            }
        } elseif ($timeframe === 'monthly' || ($timeframe === 'custom' && isset($start) && $start->diffInDays($end) <= 31)) {
            $days = $timeframe === 'monthly' ? now()->daysInMonth : $start->diffInDays($end) + 1;
            $current = $timeframe === 'monthly' ? now()->startOfMonth() : $start;
            for ($i = 0; $i < $days; $i++) {
                $date = $current->copy()->addDays($i)->toDateString();
                $chartLabels[] = $current->copy()->addDays($i)->format('j');
                $chartData[] = (clone $quotesQuery)->where('status', 'accepted')->whereDate('created_at', $date)->sum('total_amount');
            }
        } elseif ($timeframe === 'yearly') {
            for ($i = 1; $i <= 12; $i++) {
                $monthStart = now()->startOfYear()->addMonths($i - 1)->startOfMonth();
                $monthEnd = $monthStart->copy()->endOfMonth();
                $chartLabels[] = $monthStart->format('M');
                $chartData[] = (clone $quotesQuery)->where('status', 'accepted')->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
            }
        } else {
            // Default to last 7 days for 'all' or long 'custom'
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $chartLabels[] = now()->subDays($i)->format('D');
                $chartData[] = (clone $quotesQuery)->where('status', 'accepted')->whereDate('created_at', $date)->sum('total_amount');
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
                ->withCount(['quotes' => function ($q) use ($timeframe, $start, $end) {
                    if ($timeframe !== 'all') {
                        $q->whereBetween('created_at', [$start, $end]);
                    }
                }])
                ->withSum(['quotes' => function ($q) use ($timeframe, $start, $end) {
                    if ($timeframe !== 'all') {
                        $q->whereBetween('created_at', [$start, $end]);
                    }
                    $q->where('status', 'accepted');
                }], 'total_amount')
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

        $quoteStats = [
            'total_quotes' => $totalQuotes,
            'total_revenue' => $lifetimeRevenue,
            'filtered_revenue' => $acceptedRevenue,
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
            'lowStockAlerts'
        ));
    }
}
