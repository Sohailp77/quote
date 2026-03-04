<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE stock_adjustments DROP CONSTRAINT IF EXISTS stock_adjustments_type_check');
        }
    }

    public function down(): void
    {
        if (config('database.default') === 'pgsql') {
            // Restore legacy check constraint for type (manual, quote, return)
            DB::statement("ALTER TABLE stock_adjustments ADD CONSTRAINT stock_adjustments_type_check CHECK (type IN ('manual', 'quote', 'return'))");
        }
    }
};
