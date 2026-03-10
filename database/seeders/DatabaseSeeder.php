<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Core Config
        $this->call([
            PlanSeeder::class,
        ]);


        // Boss account — use this to log in and manage everything
        // $user = User::firstOrCreate(
        //     ['email' => 'boss@company.com'],
        //     [
        //         'name' => 'Boss',
        //         'password' => bcrypt('boss1234'),
        //         'role' => 'boss',
        //     ]
        // );

        // 1. Superadmin account (Global)
        $adminTenant = \App\Models\Tenant::firstOrCreate(
            ['company_name' => 'SuperAdmin HQ'],
            ['is_active' => true]
        );

        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'tenant_id' => $adminTenant->id,
                'name' => 'Super Admin',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => 'boss',
                'is_superadmin' => true,
            ]
        );

        // 2. Business Tenants
        $businesses = [
            [
                'company_name' => 'TechCorp Solutions',
                'email' => 'boss1@techcorp.com',
                'name' => 'Tech Boss',
            ],
            [
                'company_name' => 'Concrete concept',
                'email' => 'boss@company.com',
                'name' => 'Boss',
            ],
            [
                'company_name' => 'RetailSoft Systems',
                'email' => 'boss3@retailsoft.com',
                'name' => 'Retail Boss',
            ],
            [
                'company_name' => 'CreativeAgency',
                'email' => 'boss3@creativeagency.com',
                'name' => 'Creative Boss',
            ],
        ];

        foreach ($businesses as $biz) {
            $tenant = \App\Models\Tenant::firstOrCreate(
                ['company_name' => $biz['company_name']],
                [
                    'is_active' => true,
                    'plan_id' => \App\Models\Plan::where('name', 'Professional')->first()?->id
                ]
            );

            User::firstOrCreate(
                ['email' => $biz['email']],
                [
                    'tenant_id' => $tenant->id,
                    'name' => $biz['name'],
                    'email_verified_at' => now(),
                    'password' => bcrypt('boss1234'),
                    'role' => 'boss',
                    'is_superadmin' => false,
                ]
            );
        }

        // 2. Load Demo Data (4-5 months history for show)
        // $this->call([
        //     DemoDataSeeder::class,
        // ]);

        $this->call([
            HardwareSeeder::class,
            // RoleSeeder::class, // if exists
        ]);
    }
}
