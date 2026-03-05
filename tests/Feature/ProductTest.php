<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\TaxRate;
use App\Models\CompanySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $boss;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Users
        $this->boss = User::factory()->create(['role' => 'boss']);
        $this->user = User::factory()->create(['role' => 'employee']);

        // Set company settings (to prevent the products.show error)
        CompanySetting::updateOrCreate(['key' => 'currency_symbol'], ['value' => '$']);
        CompanySetting::updateOrCreate(['key' => 'company_name'], ['value' => 'Test Company']);
    }

    private function createProductDependencies(): array
    {
        $category = Category::create([
            'name' => 'Test Category',
            'type' => 'goods',
            'metric_type' => 'unit',
            'unit_name' => 'piece'
        ]);

        $taxRate = TaxRate::create([
            'name' => 'Standard Tax',
            'rate' => 10.0,
            'is_active' => true,
        ]);

        return [$category, $taxRate];
    }

    public function test_products_index_can_be_accessed(): void
    {
        $response = $this->actingAs($this->user)->get('/products');
        $response->assertStatus(200);
    }

    public function test_product_can_be_created_by_employee(): void
    {
        Storage::fake('public');
        [$category, $taxRate] = $this->createProductDependencies();

        $response = $this->actingAs($this->user)->post('/products', [
            'name' => 'New Test Product',
            'category_id' => $category->id,
            'tax_rate_id' => $taxRate->id,
            'price' => 100.00,
            'opening_stock' => 10,
            'unit_size' => 1,
            'description' => 'A test product',
            'image' => UploadedFile::fake()->image('product.jpg')
        ]);

        $response->assertRedirect('/products');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'New Test Product',
            'price' => 100.00,
            'stock_quantity' => 10,
        ]);

        $product = Product::first();
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $product->image_path));
    }

    public function test_product_show_page_renders_without_app_settings_error(): void
    {
        [$category, $taxRate] = $this->createProductDependencies();

        $product = Product::create([
            'category_id' => $category->id,
            'tax_rate_id' => $taxRate->id,
            'name' => 'Show Test Product',
            'price' => 150.00,
            'stock_quantity' => 5,
        ]);

        $response = $this->actingAs($this->user)->get("/products/{$product->id}");
        $response->assertStatus(200);
        $response->assertSee('Show Test Product');
    }

    public function test_product_can_be_updated(): void
    {
        [$category, $taxRate] = $this->createProductDependencies();

        $product = Product::create([
            'category_id' => $category->id,
            'tax_rate_id' => $taxRate->id,
            'name' => 'Old Product Name',
            'price' => 50.00,
            'stock_quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->patch("/products/{$product->id}", [
            'name' => 'Updated Product Name',
            'price' => 75.00,
        ]);

        $response->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'price' => 75.00,
        ]);

        // Stock shouldn't be updated via standard patch, testing the unset behavior
        $this->assertEquals(2, $product->fresh()->stock_quantity);
    }

    public function test_product_can_be_deleted(): void
    {
        [$category, $taxRate] = $this->createProductDependencies();

        $product = Product::create([
            'category_id' => $category->id,
            'tax_rate_id' => $taxRate->id,
            'name' => 'Product To Delete',
            'price' => 50.00,
            'stock_quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->delete("/products/{$product->id}");

        $response->assertRedirect('/products');
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
