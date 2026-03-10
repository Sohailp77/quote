<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Quotes
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('reference_id')->unique();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
            
            // Payment Tracking
            $table->string('payment_status')->default('pending');
            $table->string('payment_method')->nullable();

            // Financials
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('tax_mode')->default('exclusive');
            $table->json('tax_config_snapshot')->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('delivery_charge', 15, 2)->default(0);
            $table->decimal('additional_charge', 15, 2)->default(0);
            $table->string('additional_charge_label')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->decimal('profit_amount', 15, 2)->default(0);

            // Logistics
            $table->date('delivery_date')->nullable();
            $table->string('delivery_time')->nullable();
            $table->string('delivery_partner')->nullable();
            $table->string('tracking_number')->nullable();
            $table->enum('delivery_status', ['pending', 'shipped', 'delivered'])->nullable();
            $table->text('delivery_note')->nullable();

            // Metadata
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('template_name')->default('default');
            $table->json('display_settings')->nullable();
            $table->json('custom_fields')->nullable();
            
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });

        // 2. Quote Items
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name')->nullable();
            $table->string('variant_name')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->string('section_name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->timestamps();
        });

        // 3. Stock Adjustments
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantity_change');
            $table->string('type'); 
            $table->string('reason')->nullable();
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->integer('stock_after')->nullable();
            $table->timestamp('reverted_at')->nullable();
            $table->timestamps();
        });

        // 4. Revenues
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('stock_adjustment_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamp('reverted_at')->nullable();
            $table->timestamps();
        });

        // 5. Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->enum('status', ['pending', 'ordered', 'transit', 'received', 'cancelled'])->default('pending');
            $table->date('estimated_arrival')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('revenues');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
    }
};
