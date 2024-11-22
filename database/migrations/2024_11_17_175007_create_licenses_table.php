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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الرخصة
            $table->date('licenseDate'); // تاريخ الإصدار
            $table->date('endDate'); // تاريخ الانتهاء
            $table->string('status')->default('valid'); // الحالة (صحيحة أو منتهية)
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
