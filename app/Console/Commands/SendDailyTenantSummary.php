<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Quote;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Mail\DailyTenantSummaryMail;
use App\Services\Mail\MultiSmtpMailer;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendDailyTenantSummary extends Command
{
    protected $signature = 'tenant:send-daily-summary';
    protected $description = 'Sends a daily activity and inventory summary to all tenant owners.';

    public function handle()
    {
        $tenants = Tenant::all();
        $today = Carbon::today();
        $mailer = app(MultiSmtpMailer::class);

        foreach ($tenants as $tenant) {
            $this->info("Processing tenant: {$tenant->name}");

            // 1. Find the bosses for this tenant
            $bosses = User::where('tenant_id', $tenant->id)->where('role', 'boss')->get();
            if ($bosses->isEmpty()) {
                $this->warn("No boss found for tenant: {$tenant->name}");
                continue;
            }

            // 2. Gather Stats for today (Global scope/Tenant scope should handle filtering if traits are used)
            // But we need to be careful with scopes in console commands.
            // Many models use HasTenant trait which usually applies a Global Scope based on auth()->user()->tenant_id.
            // In a command, auth()->user() is null.
            
            // Let's query explicitly without scopes to be safe or use withoutGlobalScopes if needed.
            // Actually, we WANT to filter by tenant_id.
            
            $quotes = Quote::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->whereDate('created_at', $today)
                ->get();

            $pos = PurchaseOrder::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->whereDate('created_at', $today)
                ->count();

            $customersCount = Customer::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->whereDate('created_at', $today)
                ->count();

            // 3. Gather Low Stock Items (Current Status)
            $lowStockProducts = Product::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->get()
                ->filter(fn(\App\Models\Product $p) => $p->isLowStock())
                ->map(fn($p) => [
                    'name' => $p->name,
                    'stock' => $p->stock_quantity
                ]);

            $lowStockVariants = ProductVariant::withoutGlobalScopes()
                ->whereHas('product', function($q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id);
                })
                ->get()
                ->filter(fn(\App\Models\ProductVariant $v) => $v->isLowStock())
                ->map(fn($v) => [
                    'name' => "{$v->product->name} - {$v->name}",
                    'stock' => $v->stock_quantity
                ]);

            $lowStockItems = $lowStockProducts->concat($lowStockVariants)->toArray();

            // 4. Prepare data
            $data = [
                'quotes_count' => $quotes->count(),
                'quotes_total' => $quotes->sum('total_amount'),
                'pos_count' => $pos,
                'customers_count' => $customersCount,
                'low_stock_items' => $lowStockItems,
                'currency' => $tenant->settings->currency_symbol ?? '₹',
            ];

            // 5. Send Email to each boss
            foreach ($bosses as $boss) {
                try {
                    $mailer->sendMailable($boss->email, new DailyTenantSummaryMail($data));
                } catch (\Exception $e) {
                    Log::error("Failed to send daily summary to boss {$boss->email} for tenant {$tenant->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Daily summaries sent successfully.");
    }
}
