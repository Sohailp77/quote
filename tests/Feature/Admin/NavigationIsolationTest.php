<?php

namespace Tests\Feature\Admin;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationIsolationTest extends TestCase
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
    public function superadmin_does_not_see_tenant_links(): void
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Categories');
        $response->assertDontSee('Products');
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Tenants');
        $response->assertSee('Plans');
    }

    /** @test */
    public function regular_user_sees_tenant_links(): void
    {
        $tenant = Tenant::create(['company_name' => 'Acme Corp', 'is_active' => true]);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Business Owner',
            'email' => 'owner@acme.com',
            'password' => bcrypt('password'),
            'role' => 'boss',
            'is_superadmin' => false,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Categories');
        $response->assertSee('Products');
        $response->assertDontSee('Admin Dashboard');
        $response->assertDontSee('Tenants');
    }
}
