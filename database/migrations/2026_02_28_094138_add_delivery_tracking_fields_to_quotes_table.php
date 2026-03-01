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
        Schema::table('quotes', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('status');
            $table->time('delivery_time')->nullable()->after('delivery_date');
            $table->string('delivery_partner')->nullable()->after('delivery_time');
            $table->string('tracking_number')->nullable()->after('delivery_partner');
            $table->string('delivery_status')->nullable()->after('tracking_number'); // e.g., 'pending', 'shipped', 'delivered'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_date',
                'delivery_time',
                'delivery_partner',
                'tracking_number',
                'delivery_status'
            ]);
        });
    }
};
