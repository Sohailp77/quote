<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Quote;
use App\Models\Revenue;
use App\Models\CompanySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    private User $boss;
    private User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->boss = User::factory()->create(['role' => 'boss']);
        $this->employee = User::factory()->create(['role' => 'employee']);

        CompanySetting::updateOrCreate(['key' => 'currency_symbol'], ['group' => 'general', 'value' => '$']);
        CompanySetting::updateOrCreate(['key' => 'tax_strategy'], ['group' => 'tax', 'value' => 'split']);
    }

    private function createProductWithStock(int $stock): Product
    {
        $category = Category::create([
            'name' => 'Quote Test Category',
            'type' => 'goods',
            'metric_type' => 'unit',
            'unit_name' => 'box',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Product for Quote',
            'price' => 100.00,
            'stock_quantity' => $stock,
        ]);
    }

    public function test_quote_can_be_created(): void
    {
        $product = $this->createProductWithStock(50);

        $response = $this->actingAs($this->employee)->post('/quotes', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'tax_mode' => 'global',
            'gst_rate' => 10,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 100,
                ]
            ],
            'discount_percentage' => 0,
        ]);

        $response->assertRedirect('/quotes/create');
        $this->assertDatabaseHas('quotes', [
            'customer_name' => 'John Doe',
            'status' => 'draft',
            'subtotal' => 200,
            'tax_amount' => 20,
            'total_amount' => 220,
        ]);

        $quote = Quote::first();
        $this->assertDatabaseHas('quote_items', [
            'quote_id' => $quote->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_quote_status_can_be_updated_to_sent(): void
    {
        $product = $this->createProductWithStock(50);

        $quote = Quote::create([
            'user_id' => $this->boss->id,
            'customer_name' => 'Test Customer',
            'tax_mode' => 'global',
            'status' => 'draft',
            'total_amount' => 100,
        ]);

        $response = $this->actingAs($this->boss)->patch("/quotes/{$quote->id}/status", [
            'status' => 'sent',
        ]);

        $response->assertRedirect();
        $this->assertEquals('sent', $quote->fresh()->status);
    }

    public function test_quote_acceptance_deducts_stock_and_records_revenue(): void
    {
        $product = $this->createProductWithStock(50);

        $quote = Quote::create([
            'user_id' => $this->boss->id,
            'customer_name' => 'Test Customer',
            'tax_mode' => 'global',
            'status' => 'sent',
            'total_amount' => 100,
        ]);

        $quote->items()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => 20,
        ]);

        $response = $this->actingAs($this->boss)->patch("/quotes/{$quote->id}/status", [
            'status' => 'accepted',
        ]);

        $response->assertRedirect();

        // Assert status changed
        $this->assertEquals('accepted', $quote->fresh()->status);

        // Assert stock deducted
        $this->assertEquals(45, $product->fresh()->stock_quantity);

        // Assert revenue recorded
        $this->assertDatabaseHas('revenues', [
            'quote_id' => $quote->id,
            'amount' => 100,
        ]);

        // Assert stock adjustment record created
        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'quantity_change' => -5,
            'type' => 'quote',
            'quote_id' => $quote->id,
        ]);
    }

    public function test_quote_rejection_reverts_stock_and_revenue(): void
    {
        $product = $this->createProductWithStock(50);

        $quote = Quote::create([
            'user_id' => $this->boss->id,
            'customer_name' => 'Test Customer',
            'tax_mode' => 'global',
            'status' => 'sent',
            'total_amount' => 100,
        ]);

        $quote->items()->create([
            'product_id' => $product->id,
            'quantity' => 10,
            'price' => 10,
        ]);

        // Accept the quote first
        $this->actingAs($this->boss)->patch("/quotes/{$quote->id}/status", [
            'status' => 'accepted',
            'force' => true,
        ]);

        $this->assertEquals(40, $product->fresh()->stock_quantity);

        // Now move it away from accepted (e.g., rejected)
        $response = $this->actingAs($this->boss)->patch("/quotes/{$quote->id}/status", [
            'status' => 'rejected',
        ]);

        $response->assertRedirect();

        // Stock should be reverted back to 50
        $this->assertEquals(50, $product->fresh()->stock_quantity);

        // Revenue should be marked as reverted
        $revenue = Revenue::where('quote_id', $quote->id)->first();
        $this->assertNotNull($revenue->reverted_at);

        // Stock adjustment original should be reverted
        $adjustment = \App\Models\StockAdjustment::where('quote_id', $quote->id)->where('type', 'quote')->first();
        $this->assertNotNull($adjustment->reverted_at);

        // And a counter adjustment should exist
        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'return',
            'quote_id' => $quote->id,
            'quantity_change' => 10,
        ]);
    }

    public function test_pdf_can_be_generated(): void
    {
        $product = $this->createProductWithStock(50);

        $quote = Quote::create([
            'user_id' => $this->boss->id,
            'customer_name' => 'Test Customer',
            'tax_mode' => 'global',
            'status' => 'draft',
            'total_amount' => 100,
        ]);

        $response = $this->actingAs($this->boss)->get("/quotes/{$quote->id}/pdf");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
    public function test_employee_can_reject_their_own_quote(): void
    {
        $quote = Quote::create([
            'user_id' => $this->employee->id,
            'customer_name' => 'Employee Customer',
            'tax_mode' => 'global',
            'status' => 'sent',
            'total_amount' => 100,
        ]);

        $response = $this->actingAs($this->employee)->patch("/quotes/{$quote->id}/status", [
            'status' => 'rejected',
        ]);

        $response->assertRedirect();
        $this->assertEquals('rejected', $quote->fresh()->status);
    }

    public function test_employee_cannot_reject_others_quote(): void
    {
        $otherEmployee = User::factory()->create(['role' => 'employee']);
        $quote = Quote::create([
            'user_id' => $otherEmployee->id,
            'customer_name' => 'Other Customer',
            'tax_mode' => 'global',
            'status' => 'sent',
            'total_amount' => 100,
        ]);

        $response = $this->actingAs($this->employee)->patch("/quotes/{$quote->id}/status", [
            'status' => 'rejected',
        ]);

        $response->assertStatus(403);
    }

    public function test_boss_can_reject_accepted_quote(): void
    {
        $quote = Quote::create([
            'user_id' => $this->employee->id,
            'customer_name' => 'John Doe',
            'tax_mode' => 'global',
            'status' => 'accepted',
            'total_amount' => 100,
        ]);

        $response = $this->actingAs($this->boss)->patch("/quotes/{$quote->id}/status", [
            'status' => 'rejected',
        ]);

        $response->assertRedirect();
        $this->assertEquals('rejected', $quote->fresh()->status);
    }

    public function test_boss_can_update_logistics_even_if_status_is_already_accepted(): void
    {
        $quote = Quote::create([
            'user_id' => $this->employee->id,
            'customer_name' => 'John Doe',
            'tax_mode' => 'global',
            'status' => 'accepted',
            'total_amount' => 100,
        ]);

        $response = $this->actingAs($this->boss)->patch("/quotes/{$quote->id}/status", [
            'status' => 'accepted',
            'delivery_partner' => 'BlueDart',
            'tracking_number' => 'BD123',
        ]);

        $response->assertRedirect();
        $this->assertEquals('BlueDart', $quote->fresh()->delivery_partner);
        $this->assertEquals('BD123', $quote->fresh()->tracking_number);
    }
}
