<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Boss account — use this to log in and manage everything
        User::firstOrCreate(
            ['email' => 'boss@company.com'],
            [
                'name' => 'Boss',
                'password' => bcrypt('boss1234'),
                'role' => 'boss',
            ]
        );
    }
}
