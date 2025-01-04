<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ScannerUserSeeder extends Seeder
{
    public function run()
    {
        // إنشاء دور جديد للعارض
        $scannerRole = Role::create(['name' => 'scanner']);
        
        // إنشاء صلاحيات للعارض
        $scanPermission = Permission::create(['name' => 'scan_licenses']);
        $viewPermission = Permission::create(['name' => 'view_licenses']);
        
        // ربط الصلاحيات بالدور
        $scannerRole->givePermissionTo([$scanPermission, $viewPermission]);

        // إنشاء مستخدم جديد للعارض
        $user = User::create([
            'name' => 'عارض الرخص',
            'email' => 'h@h.com',
            'password' => Hash::make('12345678'),
        ]);

        // إعطاء المستخدم دور العارض
        $user->assignRole('scanner');
    }
}
