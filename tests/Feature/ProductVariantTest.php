<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductVariantTest extends TestCase
{
    use RefreshDatabase;

    private User $boss;

    protected function setUp(): void
    {
        parent::setUp();
        $this->boss = User::factory()->create(['role' => 'boss']);
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'Variant Test Category',
            'type' => 'goods',
            'metric_type' => 'unit',
            'unit_name' => 'box'
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Parent Product',
            'price' => 100.00,
            'stock_quantity' => 10,
        ]);
    }

    public function test_variant_can_be_added_to_product(): void
    {
        $product = $this->createProduct();
        Storage::fake('public');

        $response = $this->actingAs($this->boss)->post('/product-variants', [
            'product_id' => $product->id,
            'name' => 'Red Variant',
            'sku' => 'RED-001',
            'stock_quantity' => 15,
            'variant_price' => 110.00,
            'image' => UploadedFile::fake()->image('variant.jpg')
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'name' => 'Red Variant',
            'sku' => 'RED-001',
            'stock_quantity' => 15,
            'variant_price' => 110.00,
        ]);

        $variant = ProductVariant::first();
        $this->assertNotNull($variant->image_path);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $variant->image_path));
    }

    public function test_variant_can_be_updated(): void
    {
        $product = $this->createProduct();

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Blue Variant',
            'sku' => 'BLUE-001',
            'stock_quantity' => 5,
            'variant_price' => 105.00,
        ]);

        $response = $this->actingAs($this->boss)->patch("/product-variants/{$variant->id}", [
            'name' => 'Dark Blue Variant',
            'sku' => 'DBLUE-001',
            'stock_quantity' => 20,
            'variant_price' => 115.00,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'name' => 'Dark Blue Variant',
            'sku' => 'DBLUE-001',
            'stock_quantity' => 20,
            'variant_price' => 115.00,
        ]);
    }

    public function test_variant_can_be_deleted(): void
    {
        $product = $this->createProduct();

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Variant To Delete',
            'stock_quantity' => 5,
        ]);

        $response = $this->actingAs($this->boss)->delete("/product-variants/{$variant->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('product_variants', [
            'id' => $variant->id,
        ]);
    }
}
