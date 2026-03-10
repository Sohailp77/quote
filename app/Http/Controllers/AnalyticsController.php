<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Revenue;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CompanySetting;
use App\Models\StockAdjustment;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalRevenue = Revenue::whereNull('reverted_at')->sum('amount');

        // Monthly Revenue (Last 6 months)
        $isSqlite = DB::getDriverName() === 'sqlite';
        $monthFormat = $isSqlite ? "strftime('%Y-%m', recorded_at)" : "DATE_FORMAT(recorded_at, '%Y-%m')";

        $monthlyRevenue = Revenue::whereNull('reverted_at')
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw("$monthFormat as month")
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        // Projections
        $recentMonths = $monthlyRevenue->take(3);
        $avgMonthlyRevenue = $recentMonths->count() > 0
            ? $recentMonths->avg('total')
            : 0;

        $nextMonthProjection = $avgMonthlyRevenue;
        $nextYearProjection = $avgMonthlyRevenue * 12;

        // Top Selling Products
        $topProducts = QuoteItem::select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(quantity * price) as revenue'))
            ->whereHas('quote', fn($q) => $q->where('status', 'accepted'))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        // Costs Calculation (Stock Restocks + Losses)
        $totalCosts = StockAdjustment::whereNull('reverted_at')
            ->whereNotNull('unit_cost')
            ->select(DB::raw('SUM(ABS(quantity_change) * unit_cost) as total'))
            ->value('total') ?? 0;

        $now = Carbon::now();
        // Monthly Costs (including losses)
        $totalMonthlyCosts = StockAdjustment::whereNull('reverted_at')
            ->whereNotNull('unit_cost')
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->select(DB::raw('SUM(ABS(quantity_change) * unit_cost) as total'))
            ->value('total') ?? 0;

        // Monthly Stock Budget (only purchases/restocks)
        $monthlyRestockCosts = StockAdjustment::whereNull('reverted_at')
            ->where('quantity_change', '>', 0)
            ->whereNotNull('unit_cost')
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->select(DB::raw('SUM(quantity_change * unit_cost) as total'))
            ->value('total') ?? 0;

        // Goals
        $businessGoals = CompanySetting::where('group', 'goals')->pluck('value', 'key')->all();
        $revenueGoal = (float) ($businessGoals['monthly_revenue_goal'] ?? 0);
        $budgetGoal = (float) ($businessGoals['monthly_stock_cost_budget'] ?? 0);
        $convGoal = (float) ($businessGoals['conversion_rate_goal'] ?? 0);

        // Current Month Revenue
        $currentMonthRev = Revenue::whereNull('reverted_at')->whereBetween('recorded_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('amount');
        $lastMonthRev = Revenue::whereNull('reverted_at')->whereBetween('recorded_at', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()])->sum('amount');

        $growthRate = $lastMonthRev > 0
            ? (($currentMonthRev - $lastMonthRev) / $lastMonthRev) * 100
            : 0;

        // Conversion Rate
        $totalQuotes = Quote::whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count();
        $acceptedQuotes = Quote::where('status', 'accepted')
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();
        $currentConvRate = $totalQuotes > 0 ? ($acceptedQuotes / $totalQuotes) * 100 : 0;

        return view('analytics.index', [
            'stats' => [
                'total_revenue' => (float) $totalRevenue,
                'total_costs' => (float) $totalCosts,
                'net_profit' => (float) ($totalRevenue - $totalCosts),
                'monthly_revenue' => $monthlyRevenue,
                'projections' => [
                    'next_month' => round($nextMonthProjection, 2),
                    'next_year' => round($nextYearProjection, 2),
                    'avg_monthly' => round($avgMonthlyRevenue, 2),
                ],
                'growth_rate' => round($growthRate, 1),
                'top_products' => $topProducts,
                'goals' => [
                    'revenue' => [
                        'target' => $revenueGoal,
                        'current' => (float) $currentMonthRev,
                        'percent' => $revenueGoal > 0 ? min(100, round(($currentMonthRev / $revenueGoal) * 100, 1)) : 0
                    ],
                    'budget' => [
                        'target' => $budgetGoal,
                        'current' => (float) $monthlyRestockCosts,
                        'percent' => $budgetGoal > 0 ? min(100, round(($monthlyRestockCosts / $budgetGoal) * 100, 1)) : 0
                    ],
                    'conversion' => [
                        'target' => $convGoal,
                        'current' => round($currentConvRate, 1),
                        'percent' => $convGoal > 0 ? min(100, round(($currentConvRate / $convGoal) * 100, 1)) : 0
                    ]
                ]
            ]
        ]);
    }

}
