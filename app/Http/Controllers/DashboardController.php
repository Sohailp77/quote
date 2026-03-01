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
        $validTimeframes = ['weekly', 'monthly', 'yearly', 'all'];
        if (!in_array($timeframe, $validTimeframes)) {
            $timeframe = 'monthly';
        }

        $user = auth()->user();
        $isBoss = $user->isBoss();
        $userRole = $isBoss ? 'boss' : 'employee';

        $quotesQuery = Quote::query();
        if (!$isBoss) {
            $quotesQuery->where('user_id', $user->id);
        }

        // Global KPIs
        $totalQuotes = (clone $quotesQuery)->count();
        $totalRevenueList = clone $quotesQuery;
        $totalRevenue = $totalRevenueList->where('status', 'accepted')->sum('total_amount');
        $avgDealSize = $totalQuotes > 0 ? $totalRevenue / $totalQuotes : 0;

        $statusBreakdown = (clone $quotesQuery)->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $acceptedCount = $statusBreakdown['accepted'] ?? 0;
        $sentCount = $statusBreakdown['sent'] ?? 0;
        $draftCount = $statusBreakdown['draft'] ?? 0;
        $rejectedCount = $statusBreakdown['rejected'] ?? 0;
        $expiredCount = $statusBreakdown['expired'] ?? 0;
        $conversionRate = $totalQuotes > 0 ? round(($acceptedCount / $totalQuotes) * 100, 1) : 0;

        // Timeframe filter logic
        $filteredQuotesQuery = clone $quotesQuery;
        if ($timeframe !== 'all') {
            $start = match ($timeframe) {
                'weekly' => now()->startOfWeek(),
                'monthly' => now()->startOfMonth(),
                'yearly' => now()->startOfYear(),
            };
            $end = match ($timeframe) {
                'weekly' => now()->endOfWeek(),
                'monthly' => now()->endOfMonth(),
                'yearly' => now()->endOfYear(),
            };
            $filteredQuotesQuery->whereBetween('created_at', [$start, $end]);
        }

        $filteredRevenueQuery = clone $filteredQuotesQuery;
        $currentRevenue = $filteredRevenueQuery->where('status', 'accepted')->sum('total_amount');

        $growth = null;
        if ($timeframe === 'monthly' && $isBoss) {
            $lastMonthRevenue = (clone $quotesQuery)->where('status', 'accepted')->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])->sum('total_amount');
            $growth = $lastMonthRevenue > 0 ? round((($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : null;
        }

        // Charts & Tables
        $dailyRevenue = collect(range(6, 0))->map(function ($daysAgo) use ($quotesQuery) {
            $date = now()->subDays($daysAgo)->toDateString();
            return (clone $quotesQuery)->where('status', 'accepted')->whereDate('created_at', $date)->sum('total_amount');
        })->values()->toArray();

        // Paginated Recent Quotes (Filtered by timeframe)
        $recentQuotes = $filteredQuotesQuery->with('user:id,name')->latest()->paginate(5);
        $recentQuotes->appends(['timeframe' => $timeframe]);

        // Top Products
        $topProductsQuery = QuoteItem::select('product_id', DB::raw('COUNT(*) as quote_count'), DB::raw('SUM(quantity) as total_qty'))
            ->with('product:id,name,image_path,stock_quantity')
            ->groupBy('product_id')->orderByDesc('quote_count')->take(5);

        if (!$isBoss) {
            $topProductsQuery->whereHas('quote', fn($q) => $q->where('user_id', $user->id));
        }
        $topProducts = $topProductsQuery->get();

        // Boss Specific Data
        $employeePerformance = collect();
        $lowStockAlerts = collect();

        if ($isBoss) {
            $employeePerformance = User::where('role', 'employee')
                ->withCount('quotes')
                ->withSum('quotes', 'total_amount')
                ->withCount(['quotes as accepted_quotes_count' => fn($q) => $q->where('status', 'accepted')])
                ->orderByDesc('quotes_sum_total_amount')
                ->get(['id', 'name', 'email']);

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
            'total_revenue' => $totalRevenue,
            'filtered_revenue' => $currentRevenue,
            'avg_deal_size' => $avgDealSize,
            'conversion_rate' => $conversionRate,
            'accepted_count' => $acceptedCount,
            'sent_count' => $sentCount,
            'draft_count' => $draftCount,
            'rejected_count' => $rejectedCount,
            'expired_count' => $expiredCount,
            'recent_quotes' => $recentQuotes,
            'top_products' => $topProducts,
            'daily_revenue' => $dailyRevenue,
            'growth' => $growth,
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
