<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleSeeder extends Seeder
{
    public function run()
    {
        // إنشاء الأدوار
        $adminRole = Role::create(['name' => 'المدير النظام']);
        $licenseManagerRole = Role::create(['name' => 'مدير مكتب التراخيص']);
        $municipalGuardRole = Role::create(['name' => 'مدير مكتب الحرس البلدي']);

        // إنشاء الصلاحيات
        $manageUsersPermission = Permission::create(['name' => 'manage users']);
        $viewReportsPermission = Permission::create(['name' => 'view reports']);

        // تعيين الصلاحيات للأدوار
        $adminRole->givePermissionTo([$manageUsersPermission, $viewReportsPermission]);
        $licenseManagerRole->givePermissionTo([$viewReportsPermission]);
    }
}
