<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Customer Details
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();

            // Quote Details
            $table->string('reference_id')->unique()->nullable();
            $table->string('status')->default('draft');

            // Tax & Totals
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('gst_rate', 5, 2)->default(18.00); // e.g. 18.00
            $table->string('gst_type')->default('igst'); // igst or cgst_sgst
            $table->string('tax_mode')->default('item_level'); // 'global' or 'item_level'
            $table->json('tax_config_snapshot')->nullable(); // Stores logic used at creation time
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
