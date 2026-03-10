<?php

namespace Tests\Feature\Admin;

use App\Models\Tenant;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function createSuperAdmin(): User
    {
        $tenant = Tenant::create([
            'company_name' => 'SuperAdmin HQ',
            'is_active' => true,
        ]);

        return User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'boss',
            'is_superadmin' => true,
        ]);
    }

    /** @test */
    public function dashboard_has_security_headers(): void
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    /** @test */
    public function accessing_dashboard_creates_activity_log(): void
    {
        $admin = $this->createSuperAdmin();

        $this->actingAs($admin)->get(route('admin.dashboard'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'type' => 'ACCESS_ADMIN_PAGE',
        ]);
    }
}
