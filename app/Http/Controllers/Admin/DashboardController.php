<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalTenants = Tenant::count();
        $totalUsers = User::count();
        $plansCount = Plan::count();
        $activeTenants = Tenant::where('is_active', true)->count();

        // Platform Metrics: tenant growth per day over last 30 days
        $tenantsGrowth = Tenant::where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d'))
            ->map(fn($group) => $group->count());

        // Plan distribution  
        $planDistribution = Plan::withCount('tenants')->get()->map(fn($p) => [
            'name' => $p->name,
            'count' => $p->tenants_count,
        ]);

        // Recent tenants  
        $recentTenants = Tenant::with('plan')->latest()->limit(5)->get();

        // Paginated activity feed (10 per page, max 20 in DB)
        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->paginate(10);

        // System Health
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => \Illuminate\Foundation\Application::VERSION,
            'db_connection' => \Illuminate\Support\Facades\DB::connection()->getDatabaseName(),
            'server_ip' => request()->server('SERVER_ADDR') ?? '127.0.0.1',
            'memory_usage' => round(memory_get_usage(true) / 1048576, 2) . ' MB',
            'uptime' => now()->format('d M Y, H:i'),
        ];

        return view('admin.dashboard', compact(
            'totalTenants',
            'totalUsers',
            'plansCount',
            'activeTenants',
            'tenantsGrowth',
            'planDistribution',
            'recentTenants',
            'recentActivity',
            'systemInfo'
        ));
    }
}
