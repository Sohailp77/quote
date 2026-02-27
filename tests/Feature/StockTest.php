<?php

namespace Tests\Feature;

use App\Livewire\Products\StockPanel;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Revenue;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    private User $boss;
    private User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->boss = User::factory()->create(['role' => 'boss']);
        $this->employee = User::factory()->create(['role' => 'employee']);
    }

    private function createProductWithStock(int $stock = 10): Product
    {
        $category = Category::create([
            'name' => 'Stock Test Category',
            'type' => 'goods',
            'metric_type' => 'unit',
            'unit_name' => 'piece',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Stocked Product',
            'price' => 50.00,
            'stock_quantity' => $stock,
        ]);
    }

    public function test_stock_can_be_added_by_boss()
    {
        $product = $this->createProductWithStock(10);

        Livewire::actingAs($this->boss)
            ->test(StockPanel::class, ['product' => $product, 'appSettings' => [], 'isBoss' => true])
            ->set('quantity', 5)
            ->set('direction', 'add')
            ->set('transactionType', 'adjustment')
            ->set('reason', 'Found extra in warehouse')
            ->call('save')
            ->assertHasNoErrors();
        // ->assertSessionHas('success');

        $this->assertEquals(15, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'quantity_change' => 5,
            'type' => 'manual',
            'reason' => 'Found extra in warehouse (Original Type: adjustment)',
        ]);
    }

    public function test_stock_deduction_and_revenue_generation_for_sale()
    {
        $product = $this->createProductWithStock(20);

        Livewire::actingAs($this->boss)
            ->test(StockPanel::class, ['product' => $product, 'appSettings' => [], 'isBoss' => true])
            ->set('transactionType', 'sale') // should auto-set direction to deduct
            ->set('quantity', 3)
            ->set('amount', 150) // Revenue from sale
            ->set('reason', 'Over counter sale')
            ->call('save')
            ->assertHasNoErrors();


        $this->assertEquals(17, $product->fresh()->stock_quantity);

        $adjustment = clone StockAdjustment::first();
        $this->assertNotNull($adjustment);
        $this->assertEquals(-3, $adjustment->quantity_change);

        $this->assertDatabaseHas('revenues', [
            'stock_adjustment_id' => $adjustment->id,
            'amount' => 150,
        ]);
    }

    public function test_employees_cannot_adjust_stock()
    {
        $product = $this->createProductWithStock(10);

        Livewire::actingAs($this->employee)
            ->test(StockPanel::class, ['product' => $product, 'appSettings' => [], 'isBoss' => false])
            ->set('quantity', 5)
            ->set('direction', 'add')
            ->set('transactionType', 'adjustment')
            ->set('reason', 'Employee trying to adjust')
            ->call('save')
            ->assertForbidden();

        $this->assertEquals(10, $product->fresh()->stock_quantity);
    }

    public function test_stock_adjustment_can_be_reverted()
    {
        $product = $this->createProductWithStock(10);

        // First adjust stock
        Livewire::actingAs($this->boss)
            ->test(StockPanel::class, ['product' => $product, 'appSettings' => [], 'isBoss' => true])
            ->set('quantity', 5)
            ->set('direction', 'add')
            ->set('transactionType', 'adjustment')
            ->set('reason', 'Accidental add')
            ->call('save');

        $this->assertEquals(15, $product->fresh()->stock_quantity);
        $adjustment = clone StockAdjustment::first();

        // Now revert it
        Livewire::actingAs($this->boss)
            ->test(StockPanel::class, ['product' => $product, 'appSettings' => [], 'isBoss' => true])
            ->call('revert', $adjustment->id)
            ->assertHasNoErrors();
        // ->assertSessionHas('success');

        // Stock goes back
        $this->assertEquals(10, $product->fresh()->stock_quantity);

        // Check reverted_at timestamp exists on the original
        $this->assertNotNull($adjustment->fresh()->reverted_at);

        // Verify the counter adjustment exists
        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'quantity_change' => -5,
            'type' => 'manual',
        ]);
    }
}
