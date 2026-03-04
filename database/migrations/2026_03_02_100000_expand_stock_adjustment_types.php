<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // PostgreSQL specific: drop the check constraint if it exists from the previous enum definition
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE stock_adjustments DROP CONSTRAINT IF EXISTS stock_adjustments_type_check');
        }

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->string('type')->default('manual')->change();
        });

        // Recovery: Try to restore types from reason if they were mapped to manual
        $types = ['adjustment', 'sale', 'loss', 'purchase', 'reversion', 'initial_stock'];
        foreach ($types as $type) {
            DB::table('stock_adjustments')
                ->where('type', 'manual')
                ->where('reason', 'like', "%(Original Type: $type)%")
                ->update([
                    'type' => $type,
                    'reason' => DB::raw("REPLACE(reason, ' (Original Type: $type)', '')")
                ]);
        }
    }

    public function down(): void
    {
        DB::table('stock_adjustments')
            ->whereNotIn('type', ['manual', 'quote', 'return'])
            ->update(['type' => 'manual']);

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->enum('type', ['manual', 'quote', 'return'])->default('manual')->change();
        });
    }
};
