<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('license_types', function (Blueprint $table) {
            $table->string('specialization', 100)->after('type')->nullable(); // إضافة الحقل specialization
            $table->string('registration_code', 50)->after('specialization')->nullable(); // إضافة الحقل registration_code
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_types', function (Blueprint $table) {
            $table->dropColumn('specialization'); // حذف الحقل specialization
            $table->dropColumn('registration_code'); // حذف الحقل registration_code
        });
    }
};
