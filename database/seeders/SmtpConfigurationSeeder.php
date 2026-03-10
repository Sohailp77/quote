<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmtpConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\SmtpConfiguration::create([
            'name' => 'Primary Gmail',
            'host' => 'smtp.gmail.com',
            'port' => 465,
            'username' => 'your-gmail@gmail.com',
            'password' => 'your-app-password',
            'encryption' => 'ssl',
            'from_address' => 'noreply@yourdomain.com',
            'from_name' => 'CatalogApp Notifications',
            'is_active' => true,
            'priority' => 1,
        ]);

        \App\Models\SmtpConfiguration::create([
            'name' => 'Backup SendGrid',
            'host' => 'smtp.sendgrid.net',
            'port' => 587,
            'username' => 'apikey',
            'password' => 'your-sendgrid-api-key',
            'encryption' => 'tls',
            'from_address' => 'noreply@yourdomain.com',
            'from_name' => 'CatalogApp Backup',
            'is_active' => true,
            'priority' => 2,
        ]);
    }
}
