<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->text('address')->nullable();
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->text('customer_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('address');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('customer_address');
        });
    }
};
