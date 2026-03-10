<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_cannot_see_others_quotes(): void
    {
        // Tenant A
        $tenantA = Tenant::factory()->create();
        $userA = User::factory()->create(['tenant_id' => $tenantA->id]);
        $quoteA = Quote::factory()->create(['user_id' => $userA->id, 'tenant_id' => $tenantA->id]);

        // Tenant B
        $tenantB = Tenant::factory()->create();
        $userB = User::factory()->create(['tenant_id' => $tenantB->id]);
        $quoteB = Quote::factory()->create(['user_id' => $userB->id, 'tenant_id' => $tenantB->id]);

        // Acting as User A
        $this->actingAs($userA);
        
        $this->assertTrue(Quote::where('id', $quoteA->id)->exists());
        $this->assertFalse(Quote::where('id', $quoteB->id)->exists());
    }

    public function test_tenant_cannot_see_others_products(): void
    {
        $tenantA = Tenant::factory()->create();
        $userA = User::factory()->create(['tenant_id' => $tenantA->id]);
        
        $categoryA = Category::create([
            'name' => 'Cat A',
            'tenant_id' => $tenantA->id,
            'unit_name' => 'Box',
        ]);
        $productA = Product::create([
            'name' => 'Prod A',
            'category_id' => $categoryA->id,
            'price' => 10,
            'tenant_id' => $tenantA->id
        ]);

        $tenantB = Tenant::factory()->create();
        $userB = User::factory()->create(['tenant_id' => $tenantB->id]);
        $categoryB = Category::create([
            'name' => 'Cat B',
            'tenant_id' => $tenantB->id,
            'unit_name' => 'Bag',
        ]);
        $productB = Product::create([
            'name' => 'Prod B',
            'category_id' => $categoryB->id,
            'price' => 20,
            'tenant_id' => $tenantB->id
        ]);

        $this->actingAs($userA);

        $this->assertTrue(Product::where('id', $productA->id)->exists());
        $this->assertFalse(Product::where('id', $productB->id)->exists());
    }

    public function test_tenant_is_associated_with_plan(): void
    {
        $plan = Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'max_quotes' => 5,
            'max_products' => 5,
            'price' => 0,
            'currency' => 'INR',
        ]);

        $tenant = Tenant::factory()->create(['plan_id' => $plan->id]);

        $this->assertEquals($plan->id, $tenant->plan_id);
        $this->assertEquals(5, $tenant->plan->max_quotes);
    }

    public function test_tenant_cannot_exceed_quote_limit(): void
    {
        $plan = Plan::create([
            'name' => 'Limited Plan',
            'slug' => 'limited-plan',
            'max_quotes' => 1,
            'price' => 0,
            'currency' => 'INR',
        ]);

        $tenant = Tenant::factory()->create(['plan_id' => $plan->id]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        
        $category = Category::factory()->create(['tenant_id' => $tenant->id]);
        $product = Product::factory()->create(['tenant_id' => $tenant->id, 'category_id' => $category->id]);
        
        // Create one quote
        Quote::factory()->create(['user_id' => $user->id, 'tenant_id' => $tenant->id]);

        $this->actingAs($user);

        // Attempt to create second quote via controller
        $response = $this->post('/quotes', [
            'customer_name' => 'Test Customer',
            'tax_mode' => 'global',
            'items' => [['product_id' => $product->id, 'quantity' => 1, 'price' => 10]]
        ]);

        $response->assertSessionHas('error', 'You have reached the maximum number of quotes allowed for your plan. Please upgrade to create more.');
        $this->assertEquals(1, Quote::count());
    }

    public function test_tenant_cannot_exceed_product_limit(): void
    {
        $plan = Plan::create([
            'name' => 'Limited Product Plan',
            'slug' => 'limited-product-plan',
            'max_products' => 1,
            'price' => 0,
            'currency' => 'INR',
        ]);

        $tenant = Tenant::factory()->create(['plan_id' => $plan->id]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        
        $category = Category::factory()->create(['tenant_id' => $tenant->id]);
        
        // Create one product
        Product::factory()->create(['tenant_id' => $tenant->id, 'category_id' => $category->id]);

        $this->actingAs($user);

        // Attempt to create second product via controller
        $response = $this->post('/products', [
            'name' => 'Second Product',
            'category_id' => $category->id,
            'price' => 20,
        ]);

        $response->assertSessionHas('error', 'You have reached the maximum number of products allowed for your plan. Please upgrade to create more.');
        $this->assertEquals(1, Product::count());
    }

    public function test_tenant_cannot_exceed_user_limit(): void
    {
        $plan = Plan::create([
            'name' => 'Limited User Plan',
            'slug' => 'limited-user-plan',
            'max_users' => 1,
            'price' => 0,
            'currency' => 'INR',
        ]);

        $tenant = Tenant::factory()->create(['plan_id' => $plan->id]);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'role' => 'boss']);
        
        $this->actingAs($user);

        // Attempt to create second user via controller (EmployeeController)
        $response = $this->post('/employees', [
            'name' => 'Second User',
            'email' => 'second@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHas('error', 'You have reached the maximum number of users allowed for your plan. Please upgrade to create more.');
        $this->assertEquals(1, User::where('tenant_id', $tenant->id)->count());
    }
}
