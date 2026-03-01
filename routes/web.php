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
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // ─ Profile ─────────────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─ Catalog (all authenticated users) ───────────────────────────────────
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('product-variants', ProductVariantController::class)->only(['store', 'update', 'destroy']);
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);

    // ─ Quotes (scoped in controller) ────────────────────────────────────────
    Route::get('/quotes/create', \App\Livewire\Quotes\QuoteCart::class)->name('quotes.create');
    Route::get('/quotes/{quote}/edit', \App\Livewire\Quotes\QuoteCart::class)->name('quotes.edit');
    Route::get('/quotes', [\App\Http\Controllers\QuoteController::class, 'index'])->name('quotes.index');
    Route::post('/quotes', [\App\Http\Controllers\QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}/pdf', [\App\Http\Controllers\QuoteController::class, 'pdf'])->name('quotes.pdf');
    Route::patch('/quotes/{quote}/status', [\App\Http\Controllers\QuoteController::class, 'updateStatus'])->name('quotes.updateStatus');
    Route::patch('/quotes/{quote}/delivery', [\App\Http\Controllers\QuoteController::class, 'updateDelivery'])->name('quotes.updateDelivery');

    // ─ Settings (boss only) ─────────────────────────────────────────────────
    Route::middleware('boss')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/general', [\App\Http\Controllers\SettingsController::class, 'updateGeneral'])->name('settings.general.update');
        Route::post('/settings/bank', [\App\Http\Controllers\SettingsController::class, 'updateBank'])->name('settings.bank.update');
        Route::post('/settings/theme', [\App\Http\Controllers\SettingsController::class, 'updateTheme'])->name('settings.theme.update');
        Route::post('/settings/tax-config', [\App\Http\Controllers\SettingsController::class, 'updateTaxConfig'])->name('settings.tax-config.update');
        Route::post('/settings/tax-rates', [\App\Http\Controllers\SettingsController::class, 'storeTaxRate'])->name('settings.tax-rates.store');
        Route::patch('/settings/tax-rates/{taxRate}', [\App\Http\Controllers\SettingsController::class, 'updateTaxRate'])->name('settings.tax-rates.update');
        Route::delete('/settings/tax-rates/{taxRate}', [\App\Http\Controllers\SettingsController::class, 'destroyTaxRate'])->name('settings.tax-rates.destroy');
        Route::post('/settings/goals', [\App\Http\Controllers\SettingsController::class, 'updateGoals'])->name('settings.goals.update');

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
