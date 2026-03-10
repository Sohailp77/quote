<?php

namespace Tests\Feature\Admin;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantCreationTest extends TestCase
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
    public function superadmin_can_access_create_tenant_page(): void
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->get(route('admin.tenants.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function superadmin_can_create_tenant_and_owner(): void
    {
        $admin = $this->createSuperAdmin();
        $plan = Plan::create(['name' => 'Premium', 'slug' => 'premium', 'price' => 100, 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.tenants.store'), [
            'company_name' => 'New Business',
            'plan_id' => $plan->id,
            'owner_name' => 'John Doe',
            'owner_email' => 'john@example.com',
            'owner_password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.tenants.index'));
        $this->assertDatabaseHas('tenants', ['company_name' => 'New Business']);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'boss',
        ]);
    }
}
