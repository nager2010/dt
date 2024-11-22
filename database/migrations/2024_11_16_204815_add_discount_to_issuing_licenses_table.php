<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('issuing_licenses', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0); // إضافة الحقل إذا لم يكن موجودًا
        });
    }

    public function down(): void
    {
        Schema::table('issuing_licenses', function (Blueprint $table) {
            $table->dropColumn('discount'); // حذف الحقل عند التراجع
        });
    }
};
