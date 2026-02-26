<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "GST 18%", "VAT 5%"
            $table->decimal('rate', 5, 2); // e.g., 18.00
            $table->string('type')->default('percentage'); // For future proofing (fixed/percentage)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default rates
        DB::table('tax_rates')->insert([
            ['name' => 'GST 5%', 'rate' => 5.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GST 12%', 'rate' => 12.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GST 18%', 'rate' => 18.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GST 28%', 'rate' => 28.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Export (0%)', 'rate' => 0.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
