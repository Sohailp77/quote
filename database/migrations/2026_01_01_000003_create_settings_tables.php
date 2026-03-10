<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Company Settings
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('group')->default('general');
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, json, boolean, etc.
            $table->timestamps();
            
            $table->unique(['tenant_id', 'group', 'key']);
        });

        // 2. SMTP Configurations
        Schema::create('smtp_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Display name for this SMTP account');
            $table->string('host');
            $table->integer('port');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('encryption')->nullable()->default('tls');
            $table->string('from_address');
            $table->string('from_name');
            $table->boolean('is_active')->default(true);
            $table->integer('fail_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('last_fail_at')->nullable();
            $table->text('last_error')->nullable();
            $table->integer('priority')->default(0)->comment('Lower number = higher priority');
            $table->timestamps();
        });

        // 3. Tax Rates
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('rate', 5, 2);
            $table->string('type')->default('percentage');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('smtp_configurations');
        Schema::dropIfExists('company_settings');
    }
};
