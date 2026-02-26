<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StockController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

use App\Models\Category;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;

// ── Public routes ───────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => false, // Registration disabled — boss creates employees
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ── Authenticated routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ─ Dashboard (role-aware) ───────────────────────────────────────────────
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isBoss()) {
            // ── BOSS DATA ───────────────────────────────────────────────────
            $totalQuotes = Quote::count();
            $totalRevenue = Quote::where('status', 'accepted')->sum('total_amount');
            $monthlyRevenue = Quote::where('status', 'accepted')->where('created_at', '>=', now()->startOfMonth())->sum('total_amount');
            $lastMonthRevenue = Quote::where('status', 'accepted')->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])->sum('total_amount');
            $monthlyGrowth = $lastMonthRevenue > 0
                ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
                : null;
            $weeklyRevenue = Quote::where('status', 'accepted')->where('created_at', '>=', now()->startOfWeek())->sum('total_amount');
            $avgDealSize = $totalQuotes > 0 ? Quote::where('status', 'accepted')->avg('total_amount') : 0;

            $statusBreakdown = Quote::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')->pluck('count', 'status')->toArray();

            $acceptedCount = $statusBreakdown['accepted'] ?? 0;
            $sentCount = $statusBreakdown['sent'] ?? 0;
            $draftCount = $statusBreakdown['draft'] ?? 0;
            $rejectedCount = $statusBreakdown['rejected'] ?? 0;
            $expiredCount = $statusBreakdown['expired'] ?? 0;
            $conversionRate = $totalQuotes > 0 ? round(($acceptedCount / $totalQuotes) * 100, 1) : 0;

            $recentQuotes = Quote::with('user:id,name')->latest()->take(10)
                ->get(['id', 'reference_id', 'customer_name', 'total_amount', 'status', 'created_at', 'user_id']);

            $topProducts = QuoteItem::select('product_id', DB::raw('COUNT(*) as quote_count'), DB::raw('SUM(quantity) as total_qty'))
                ->with('product:id,name,image_path,stock_quantity')
                ->groupBy('product_id')->orderByDesc('quote_count')->take(5)->get();

            $dailyRevenue = collect(range(6, 0))->map(function ($daysAgo) {
                $date = now()->subDays($daysAgo)->toDateString();
                return Quote::where('status', 'accepted')->whereDate('created_at', $date)->sum('total_amount');
            })->values()->toArray();

            // Employee performance
            $employeePerformance = User::where('role', 'employee')
                ->withCount('quotes')
                ->withSum('quotes', 'total_amount')
                ->withCount(['quotes as accepted_quotes_count' => fn($q) => $q->where('status', 'accepted')])
                ->orderByDesc('quotes_sum_total_amount')
                ->get(['id', 'name', 'email']);

            // Low stock alert (Products and Variants)
            $lowStockProducts = Product::where('stock_quantity', '<=', 5)
                ->select('id', 'name', 'stock_quantity', 'sku')
                ->get()
                ->map(function ($item) {
                    $item->is_variant = false;
                    return $item;
                });

            $lowStockVariants = \App\Models\ProductVariant::where('stock_quantity', '<=', 5)
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

            return view('dashboard', [
                'userRole' => 'boss',
                'stats' => [
                    'total_categories' => Category::count(),
                    'total_products' => Product::count(),
                ],
                'quoteStats' => [
                    'total_quotes' => $totalQuotes,
                    'weekly_revenue' => $weeklyRevenue,
                    'total_revenue' => $totalRevenue,
                    'monthly_revenue' => $monthlyRevenue,
                    'last_month_revenue' => $lastMonthRevenue,
                    'monthly_growth' => $monthlyGrowth,
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
                ],
                'employeePerformance' => $employeePerformance,
                'lowStockProducts' => $lowStockAlerts,
            ]);
        }

        // ── EMPLOYEE DATA ───────────────────────────────────────────────────
        $myQuotes = Quote::where('user_id', $user->id);
        // Revenue only if accepted 
        $myTotal = $myQuotes->clone()->where('status', 'accepted')->sum('total_amount');
        $myCount = $myQuotes->clone()->count();
        $myAccepted = $myQuotes->clone()->where('status', 'accepted')->count();
        $myPending = $myQuotes->clone()->where('status', 'sent')->count();
        $myWeekRevenue = $myQuotes->clone()->where('status', 'accepted')->where('created_at', '>=', now()->startOfWeek())->sum('total_amount');
        $myConversion = $myCount > 0 ? round(($myAccepted / $myCount) * 100, 1) : 0;
        $myRecentQuotes = $myQuotes->clone()->latest()->take(8)
            ->get(['id', 'reference_id', 'customer_name', 'total_amount', 'status', 'created_at']);

        $myDailyRevenue = collect(range(6, 0))->map(function ($daysAgo) use ($user) {
            $date = now()->subDays($daysAgo)->toDateString();
            return Quote::where('user_id', $user->id)->whereDate('created_at', $date)->sum('total_amount');
        })->values()->toArray();

        $myTopProducts = QuoteItem::select('product_id', DB::raw('COUNT(*) as quote_count'))
            ->whereHas('quote', fn($q) => $q->where('user_id', $user->id))
            ->with('product:id,name,stock_quantity')
            ->groupBy('product_id')->orderByDesc('quote_count')->take(5)->get();

        return view('dashboard', [
            'userRole' => 'employee',
            'stats' => [],
            'quoteStats' => [
                'total_quotes' => $myCount,
                'weekly_revenue' => $myWeekRevenue,
                'total_revenue' => $myTotal,
                'monthly_revenue' => $myQuotes->clone()->where('status', 'accepted')->where('created_at', '>=', now()->startOfMonth())->sum('total_amount'),
                'conversion_rate' => $myConversion,
                'accepted_count' => $myAccepted,
                'sent_count' => $myPending,
                'draft_count' => $myQuotes->clone()->where('status', 'draft')->count(),
                'rejected_count' => $myQuotes->clone()->where('status', 'rejected')->count(),
                'expired_count' => 0,
                'recent_quotes' => $myRecentQuotes,
                'top_products' => $myTopProducts,
                'daily_revenue' => $myDailyRevenue,
                'avg_deal_size' => $myCount > 0 ? $myTotal / $myCount : 0,
                'monthly_growth' => null,
            ],
        ]);
    })->name('dashboard');

    // ─ Profile ─────────────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─ Catalog (all authenticated users) ───────────────────────────────────
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('product-variants', ProductVariantController::class)->only(['store', 'update', 'destroy']);

    // ─ Quotes (scoped in controller) ────────────────────────────────────────
    Route::get('/quotes/create', \App\Livewire\Quotes\QuoteCart::class)->name('quotes.create');
    Route::get('/quotes/{quote}/edit', \App\Livewire\Quotes\QuoteCart::class)->name('quotes.edit');
    Route::get('/quotes', [\App\Http\Controllers\QuoteController::class, 'index'])->name('quotes.index');
    Route::post('/quotes', [\App\Http\Controllers\QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}/pdf', [\App\Http\Controllers\QuoteController::class, 'pdf'])->name('quotes.pdf');
    Route::patch('/quotes/{quote}/status', [\App\Http\Controllers\QuoteController::class, 'updateStatus'])->name('quotes.updateStatus');

    // ─ Settings (boss only) ─────────────────────────────────────────────────
    Route::get('/settings', \App\Livewire\Settings\Index::class)->name('settings.index');
    Route::middleware('boss')->group(function () {
        Route::resource('employees', EmployeeController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::post('/stock/{product}/adjust', [StockController::class, 'adjust'])->name('stock.adjust');
        Route::patch('/stock/adjustments/{adjustment}', [StockController::class, 'update'])->name('stock.adjustments.update');
        Route::post('/stock/{adjustment}/revert', [StockController::class, 'revert'])->name('stock.revert');

        Route::patch('/revenues/{revenue}', [\App\Http\Controllers\RevenueController::class, 'update'])->name('revenues.update');
        Route::delete('/revenues/{revenue}', [\App\Http\Controllers\RevenueController::class, 'destroy'])->name('revenues.destroy');

        // Purchase Orders (Reordering)
        Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class)->only(['index', 'store', 'update']);
        Route::patch('/purchase-orders/{order}/status', [\App\Http\Controllers\PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.updateStatus');
        Route::post('/purchase-orders/{order}/confirm-received', [\App\Http\Controllers\PurchaseOrderController::class, 'confirmReceived'])->name('purchase-orders.confirm-received');

        // Analytics
        Route::get('/analytics', \App\Livewire\Analytics\Index::class)->name('analytics.index');
        Route::get('/analytics/ledger', \App\Livewire\Analytics\Ledger::class)->name('analytics.ledger');

        // Data Reset
        Route::post('/settings/start-fresh', [\App\Http\Controllers\DataResetController::class, 'resetData'])->name('settings.start-fresh');
    });
});

require __DIR__ . '/auth.php';
