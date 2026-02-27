<?php

namespace Tests\Feature;

use App\Livewire\PurchaseOrders\CreateOrder;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
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

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'PO Test Category',
            'type' => 'goods',
            'metric_type' => 'unit',
            'unit_name' => 'piece',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Need Restock Product',
            'price' => 50.00,
            'stock_quantity' => 10,
        ]);
    }

    public function test_boss_can_view_purchase_orders()
    {
        $response = $this->actingAs($this->boss)->get(route('purchase-orders.index'));
        $response->assertStatus(200);
    }

    public function test_employee_cannot_view_purchase_orders()
    {
        $response = $this->actingAs($this->employee)->get(route('purchase-orders.index'));
        $response->assertStatus(403);
    }

    public function test_boss_can_create_purchase_order_via_livewire()
    {
        $product = $this->createProduct();

        Livewire::actingAs($this->boss)
            ->test(CreateOrder::class, ['products' => collect([$product]), 'appSettings' => []])
            ->set('product_id', $product->id)
            ->set('quantity', 50)
            ->set('unit_cost', 25.50)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('purchase-orders.index'));

        $this->assertDatabaseHas('purchase_orders', [
            'product_id' => $product->id,
            'quantity' => 50,
            'unit_cost' => 25.50,
            'status' => 'pending',
            'created_by' => $this->boss->id,
        ]);
    }

    public function test_employee_cannot_create_purchase_order_via_livewire()
    {
        $product = $this->createProduct();

        Livewire::actingAs($this->employee)
            ->test(CreateOrder::class, ['products' => collect([$product]), 'appSettings' => []])
            ->set('product_id', $product->id)
            ->set('quantity', 50)
            ->set('unit_cost', 25.50)
            ->call('save')
            ->assertForbidden();

        $this->assertDatabaseEmpty('purchase_orders');
    }

    public function test_boss_can_update_status()
    {
        $product = $this->createProduct();
        $po = PurchaseOrder::create([
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_cost' => 10.00,
            'status' => 'pending',
            'created_by' => $this->boss->id,
        ]);

        $response = $this->actingAs($this->boss)->patch(route('purchase-orders.updateStatus', $po), [
            'status' => 'transit',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('transit', $po->fresh()->status);
    }

    public function test_confirming_received_updates_stock()
    {
        $product = $this->createProduct();
        $po = PurchaseOrder::create([
            'product_id' => $product->id,
            'quantity' => 25,
            'unit_cost' => 10.00,
            'status' => 'transit',
            'created_by' => $this->boss->id,
        ]);

        $initialStock = $product->stock_quantity;

        $response = $this->actingAs($this->boss)->post(route('purchase-orders.confirm-received', $po));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('received', $po->fresh()->status);
        $this->assertNotNull($po->fresh()->received_at);

        // Stock must have increased by 25
        $this->assertEquals($initialStock + 25, $product->fresh()->stock_quantity);

        // A stock adjustment must be recorded
        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'quantity_change' => 25,
            'type' => 'manual', // since purchase_order drops to manual in our model mapping
        ]);
    }
}
