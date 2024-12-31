<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء المستخدم
        $user = User::create([
            'name' => 'kaled',
            'email' => 'a@a.com',
            'password' => bcrypt('12345678'),
        ]);

        // إنشاء الدور إذا لم يكن موجودًا
        $role = Role::firstOrCreate(['name' => 'المدير النظام']);

        // تعيين الدور للمستخدم
        $user->assignRole($role);

        // إذا أردت إنشاء مستخدمين آخرين، كرر نفس العملية
    }
}
