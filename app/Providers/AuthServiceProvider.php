<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Role::class => \App\Policies\RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // تحقق من وجود الجداول قبل إنشاء الأدوار والصلاحيات
        if (!Schema::hasTable('permissions') || !Schema::hasTable('roles')) {
            return;
        }

        // إنشاء الصلاحيات الأساسية
        $permissions = [
            // طلبات التراخيص
            'view_license_request',
            'view_any_license_request',
            'create_license_request',
            'update_license_request',
            'delete_license_request',
            'delete_any_license_request',
            'approve_license_request',
            'reject_license_request',

            // التراخيص المصدرة
            'view_issuing_license',
            'view_any_issuing_license',
            'create_issuing_license',
            'update_issuing_license',
            'delete_issuing_license',
            'delete_any_issuing_license',
            'print_issuing_license',
            'scan_licenses',

            // التراخيص المنتهية
            'view_expired_license',
            'view_any_expired_license',

            // المناطق
            'view_region',
            'view_any_region',
            'create_region',
            'update_region',
            'delete_region',
            'delete_any_region',

            // البلديات
            'view_municipality',
            'view_any_municipality',
            'create_municipality',
            'update_municipality',
            'delete_municipality',
            'delete_any_municipality',

            // المستخدمين
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',

            // الإعدادات
            'manage_settings',
            'manage_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // إنشاء الأدوار وتعيين الصلاحيات
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::whereNotIn('name', [
            'delete_any_license_request',
            'delete_any_issuing_license',
            'delete_any_region',
            'delete_any_municipality',
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
            'manage_roles'
        ])->get());

        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions(Permission::where('name', 'like', 'view%')->get());

        // تعيين Gate للمدير
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
    }
}
