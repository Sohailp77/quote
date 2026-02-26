<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity_change'); // positive = added, negative = deducted
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->integer('stock_after');     // snapshot after adjustment
            $table->enum('type', ['manual', 'quote', 'return'])->default('manual');
            $table->string('reason')->nullable();
            $table->timestamp('reverted_at')->nullable();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
