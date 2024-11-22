<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('issuing_licenses', function (Blueprint $table) {
            // تحقق من وجود العمود قبل إضافته
            if (!Schema::hasColumn('issuing_licenses', 'endDate')) {
                $table->date('endDate')->nullable()->after('licenseDuration');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issuing_licenses', function (Blueprint $table) {
            // تحقق من وجود العمود قبل محاولة حذفه
            if (Schema::hasColumn('issuing_licenses', 'endDate')) {
                $table->dropColumn('endDate');
            }
        });
    }
};
