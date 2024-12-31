<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MunicipalGuardSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء دور حارس البلدية إذا لم يكن موجوداً
        $municipalGuardRole = Role::firstOrCreate(['name' => 'municipal_guard']);

        // البحث عن المستخدم أو إنشائه
        $user = User::firstOrCreate(
            ['email' => 'guard@example.com'],
            [
                'name' => 'حارس البلدية',
                'password' => bcrypt('password123'),
            ]
        );

        // التأكد من أن المستخدم لديه دور حارس البلدية
        if (!$user->hasRole('municipal_guard')) {
            $user->assignRole('municipal_guard');
        }

        // إضافة صلاحية فحص الرخص
        if (!$user->hasPermissionTo('scan_licenses')) {
            $user->givePermissionTo('scan_licenses');
        }

        $this->command->info('تم إنشاء/تحديث حساب حارس البلدية بنجاح.');
        $this->command->info('البريد الإلكتروني: guard@example.com');
        $this->command->info('كلمة المرور: password123');
    }
}
