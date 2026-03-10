<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Plans
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency')->default('INR');
            $table->integer('max_quotes')->default(-1);
            $table->integer('max_products')->default(-1);
            $table->integer('max_users')->default(-1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tenants
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('company_name');
            $table->string('domain')->unique()->nullable();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type');
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('plans');
    }
};
