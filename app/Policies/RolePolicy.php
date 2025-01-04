<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * تجاوز كل الفحوصات للمدير
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض أي أدوار
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view_roles', 'manage_roles']);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض الدور
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasAnyPermission(['view_roles', 'manage_roles']);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إنشاء أدوار
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديث الدور
     */
    public function update(User $user, Role $role): bool
    {
        // لا يمكن تعديل دور super_admin إلا من قبل super_admin نفسه
        if ($role->name === 'super_admin' && !$user->hasRole('super_admin')) {
            return false;
        }

        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حذف الدور
     */
    public function delete(User $user, Role $role): bool
    {
        // لا يمكن حذف دور super_admin
        if ($role->name === 'super_admin') {
            return false;
        }

        // لا يمكن للمستخدم حذف دوره الحالي
        if ($user->hasRole($role)) {
            return false;
        }

        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم استعادة الدور
     */
    public function restore(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حذف الدور نهائياً
     */
    public function forceDelete(User $user, Role $role): bool
    {
        // لا يمكن حذف دور super_admin نهائياً
        if ($role->name === 'super_admin') {
            return false;
        }

        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تعيين الصلاحيات للدور
     */
    public function givePermission(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم سحب الصلاحيات من الدور
     */
    public function revokePermission(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }
}
