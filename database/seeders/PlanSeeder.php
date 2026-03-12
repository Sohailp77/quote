<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Demo / Free',
                'slug' => 'demo-free',
                'description' => 'Perfect for testing our platform features.',
                'price' => 0.00,
                'currency' => 'INR',
                'max_users' => 1,
                'max_products' => 5,
                'max_quotes' => 10,
                'is_active' => true,
                'allow_email_notifications' => false,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for growing small businesses.',
                'price' => 4999.00,
                'currency' => 'INR',
                'max_users' => 3,
                'max_products' => 30,
                'max_quotes' => 1000,
                'is_active' => true,
                'allow_email_notifications' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Unlimited power for large organizations.',
                'price' => 6999.00,
                'currency' => 'INR',
                'max_users' => 5,
                'max_products' => 100,
                'max_quotes' => 99999,
                'is_active' => true,
                'allow_email_notifications' => true,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(['name' => $planData['name']], $planData);
        }
    }
}
