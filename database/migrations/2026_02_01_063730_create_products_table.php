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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->decimal('unit_size', 8, 2)->nullable(); // e.g., 1.44 (sqm/box)
            $table->string('sku')->nullable()->unique();
            $table->string('image_path')->nullable();
            $table->json('specifications')->nullable(); // Dynamic attributes
            // Note: tax_rates table is created after products, so we'll add the foreign key manually without constrained('tax_rates') for now, or just use foreignId
            $table->unsignedBigInteger('tax_rate_id')->nullable();
            $table->timestamps();

            $table->foreign('tax_rate_id')->references('id')->on('tax_rates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
