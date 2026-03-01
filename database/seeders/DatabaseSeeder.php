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
            // RoleSeeder::class, // if exists
        ]);

        // Boss account — use this to log in and manage everything
        $user = User::firstOrCreate(
            ['email' => 'boss@company.com'],
            [
                'name' => 'Boss',
                'password' => bcrypt('boss1234'),
                'role' => 'boss',
            ]
        );

        // 2. Load Demo Data (4-5 months history for show)
        $this->call([
            DemoDataSeeder::class,
        ]);
    }
}
