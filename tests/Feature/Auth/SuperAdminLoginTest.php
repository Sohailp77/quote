<?php

namespace Tests\Feature\Auth;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminLoginTest extends TestCase
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
    public function superadmin_can_login_and_redirects_to_admin_dashboard(): void
    {
        $this->createSuperAdmin();

        $response = $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function superadmin_is_redirected_from_regular_dashboard_to_admin_dashboard(): void
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function superadmin_can_access_admin_dashboard(): void
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function login_failure_returns_validation_error_not_500(): void
    {
        $this->createSuperAdmin();

        $response = $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $response->assertStatus(302); // should redirect back with error, not 500
    }

    /** @test */
    public function superadmin_has_correct_flags(): void
    {
        $admin = $this->createSuperAdmin();

        $this->assertTrue($admin->isSuperAdmin());
        $this->assertNotNull($admin->email_verified_at);
    }
}
