<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view_licenses',
            'create_licenses',
            'edit_licenses',
            'delete_licenses',
            'scan_licenses', // New permission for QR scanning
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles and assign permissions
        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo($permissions);

        $municipalGuardRole = Role::findOrCreate('municipal_guard');
        $municipalGuardRole->givePermissionTo(['view_licenses', 'scan_licenses']);
    }
}
