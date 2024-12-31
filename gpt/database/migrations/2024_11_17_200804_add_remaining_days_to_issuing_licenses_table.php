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
        Schema::table('issuing_licenses', function (Blueprint $table) {
            $table->integer('remainingDays')->nullable()->after('endDate'); // إضافة الحقل
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issuing_licenses', function (Blueprint $table) {
            $table->dropColumn('remainingDays'); // إزالة الحقل
        });
    }
};
