<?php

namespace App\Livewire\Analytics;

use App\Models\Revenue;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Product;
use App\Models\CompanySetting;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public function render()
    {
        $quoteRevenue = Quote::where('status', 'accepted')->sum('total_amount');
        $manualRevenue = Revenue::whereNull('reverted_at')->sum('amount');
        $totalRevenue = $quoteRevenue + $manualRevenue;

        // Monthly Revenue (Quotes + Manual)
        $now = Carbon::now();
        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $start = $now->copy()->subMonths($i)->startOfMonth();
            $end = $now->copy()->subMonths($i)->endOfMonth();
            
            $mQuoteRev = Quote::where('status', 'accepted')->whereBetween('accepted_at', [$start, $end])->sum('total_amount');
            $mManualRev = Revenue::whereNull('reverted_at')->whereBetween('recorded_at', [$start, $end])->sum('amount');
            
            $months[] = (object) [
                'month' => $start->format('Y-m'),
                'total' => $mQuoteRev + $mManualRev
            ];
        }
        $monthlyRevenue = collect($months);

        // Projections
        $recentMonths = $monthlyRevenue->take(3);
        $avgMonthlyRevenue = $recentMonths->count() > 0 ? $recentMonths->avg('total') : 0;
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

        // Current Month Stats
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();
        
        $currentMonthQuoteRev = Quote::where('status', 'accepted')->whereBetween('accepted_at', [$currentMonthStart, $currentMonthEnd])->sum('total_amount');
        $currentMonthManualRev = Revenue::whereNull('reverted_at')->whereBetween('recorded_at', [$currentMonthStart, $currentMonthEnd])->sum('amount');
        $currentMonthRev = $currentMonthQuoteRev + $currentMonthManualRev;

        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $lastMonthQuoteRev = Quote::where('status', 'accepted')->whereBetween('accepted_at', [$lastMonthStart, $lastMonthEnd])->sum('total_amount');
        $lastMonthManualRev = Revenue::whereNull('reverted_at')->whereBetween('recorded_at', [$lastMonthStart, $lastMonthEnd])->sum('amount');
        $lastMonthRev = $lastMonthQuoteRev + $lastMonthManualRev;

        $growthRate = $lastMonthRev > 0 ? (($currentMonthRev - $lastMonthRev) / $lastMonthRev) * 100 : 0;

        // Monthly Stock Budget
        $monthlyRestockCosts = StockAdjustment::whereNull('reverted_at')
            ->where('quantity_change', '>', 0)
            ->whereNotNull('unit_cost')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->select(DB::raw('SUM(quantity_change * unit_cost) as total'))
            ->value('total') ?? 0;

        // Goals
        $businessGoals = CompanySetting::where('group', 'goals')->pluck('value', 'key')->all();
        $revenueGoal = (float) ($businessGoals['monthly_revenue_goal'] ?? 0);
        $budgetGoal = (float) ($businessGoals['monthly_stock_cost_budget'] ?? 0);
        $convGoal = (float) ($businessGoals['conversion_rate_goal'] ?? 0);

        // Conversion Rate
        $totalQuotes = Quote::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
        $acceptedQuotes = Quote::where('status', 'accepted')
            ->whereBetween('accepted_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $currentConvRate = $totalQuotes > 0 ? ($acceptedQuotes / $totalQuotes) * 100 : 0;

        // Recent Activity Feed
        $recentActivity = collect($this->getActivity(10));

        return view('livewire.analytics.index', [
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
            ],
            'recentActivity' => $recentActivity
        ]);
    }

    private function getActivity(int $limit = 50)
    {
        $quotes = Quote::where('status', 'accepted')
            ->orderBy('accepted_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($q) {
                return [
                    'id' => 'quote_' . $q->id,
                    'is_revenue' => true,
                    'type' => 'Quote Order',
                    'amount' => (float) $q->total_amount,
                    'date' => clone $q->accepted_at,
                    'description' => "Order #{$q->reference_id} for {$q->customer_name}",
                    'target_item' => $q->items->count() . ' items',
                    'adjustment_id' => null,
                    'reverted_at' => null, // accepted quotes aren't "reverted" in this list, they change status
                ];
            });

        $revenues = Revenue::with(['quote', 'stockAdjustment.variant', 'stockAdjustment.product'])
            ->orderBy('recorded_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($rev) {
                return [
                    'id' => 'rev_' . $rev->id,
                    'is_revenue' => true,
                    'type' => $rev->quote_id ? 'Quote Payment' : 'Manual Sale',
                    'amount' => (float) $rev->amount,
                    'date' => clone $rev->recorded_at,
                    'description' => $rev->quote ? "Order #{$rev->quote->reference_id}" : ($rev->stockAdjustment ? $rev->stockAdjustment->reason : 'Direct Sale'),
                    'target_item' => $rev->stockAdjustment ? ($rev->stockAdjustment->variant ? $rev->stockAdjustment->variant->name : $rev->stockAdjustment->product->name) : '-',
                    'adjustment_id' => $rev->stock_adjustment_id,
                    'reverted_at' => $rev->reverted_at,
                ];
            });

        $costs = StockAdjustment::whereNotNull('unit_cost')
            ->with(['product', 'variant', 'user'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($adj) {
                return [
                    'id' => 'adj_' . $adj->id,
                    'is_revenue' => false,
                    'type' => ucfirst($adj->type),
                    'amount' => (float) (abs($adj->quantity_change) * $adj->unit_cost),
                    'date' => clone $adj->created_at,
                    'description' => $adj->reason,
                    'target_item' => $adj->variant ? $adj->variant->name : $adj->product->name,
                    'user' => $adj->user ? $adj->user->name : 'System',
                    'quantity' => $adj->quantity_change,
                    'unit_cost' => (float) $adj->unit_cost,
                    'reverted_at' => $adj->reverted_at,
                ];
            });

        return $quotes->concat($revenues)->concat($costs)->sortByDesc('date')->values()->take($limit);
    }
}
