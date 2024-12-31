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
        //
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

        // Create permissions if they don't exist
        $permissions = [
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'scan_licenses'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->syncPermissions(['view posts', 'create posts', 'edit posts']);

        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->syncPermissions(['view posts']);

        Gate::define('scan_licenses', function ($user) {
            return $user->hasRole(['admin', 'municipal_guard']) || $user->hasPermissionTo('scan_licenses');
        });
    }
}
