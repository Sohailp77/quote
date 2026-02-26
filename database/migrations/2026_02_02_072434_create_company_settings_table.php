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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->string('group')->index()->default('general');
            $table->timestamps();
        });

        // Seed default settings
        DB::table('company_settings')->insert([
            [
                'key' => 'tax_configuration',
                'value' => json_encode([
                    'strategy' => 'split', // 'single' or 'split'
                    'primary_label' => 'GST',
                    'secondary_labels' => ['CGST', 'SGST'], // Used if strategy is split
                ]),
                'group' => 'tax',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_symbol',
                'value' => json_encode('₹'),
                'group' => 'company',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bank_name',
                'value' => null,
                'group' => 'company',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bank_account_name',
                'value' => null,
                'group' => 'company',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bank_account_number',
                'value' => null,
                'group' => 'company',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bank_ifsc',
                'value' => null,
                'group' => 'company',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bank_branch',
                'value' => null,
                'group' => 'company',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
