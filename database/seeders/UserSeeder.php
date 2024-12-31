<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('المدير النظام');

        // Create license manager
        $licenseManager = User::create([
            'name' => 'مدير التراخيص',
            'email' => 'license@example.com',
            'password' => Hash::make('password123'),
        ]);
        $licenseManager->assignRole('مدير مكتب التراخيص');

        // Create municipal guard
        $municipalGuard = User::create([
            'name' => 'مدير الحرس',
            'email' => 'guard@example.com',
            'password' => Hash::make('password123'),
        ]);
        $municipalGuard->assignRole('مدير مكتب الحرس البلدي');
    }
}
