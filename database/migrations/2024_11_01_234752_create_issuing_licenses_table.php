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
        Schema::create('issuing_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('fullName', 255); // الاسم الرباعي
            $table->string('nationalID', 20); // الرقم الوطني
            $table->string('passportOrID', 20)->nullable(); // جواز السفر/بطاقة الهوية يمكن أن تكون فارغة
            $table->string('phoneNumber', 15); // رقم الهاتف
            $table->string('projectName', 255); // اسم المشروع
            $table->string('positionInProject', 50)->nullable(); // صفة في المشروع يمكن أن تكون فارغة
            $table->string('projectAddress', 255)->nullable(); // عنوان المشروع يمكن أن يكون فارغًا
            $table->string('municipality_id'); // معرف البلدية
            $table->string('region_id'); // معرف المحلة
            $table->string('license_type_id'); // نوع الترخيص
            $table->string('nearestLandmark', 100)->nullable(); // أقرب نقطة دالة يمكن أن تكون فارغة
            $table->date('licenseDate'); // تاريخ الإصدار
            $table->string('licenseNumber', 15); // رقم الرخصة
            $table->integer('licenseDuration'); // مدة الترخيص
            $table->integer('licenseFee'); // رسوم الترخيص
            $table->decimal('discount', 10, 2)->default(0); // الخصم يمكن أن يكون فارغًا
            $table->string('chamberOfCommerceNumber', 20)->nullable(); // رقم قيد الغرفة التجارية يمكن أن يكون فارغًا
            $table->string('taxNumber', 20)->nullable(); // الرقم الضريبي يمكن أن يكون فارغًا
            $table->string('economicNumber', 20)->nullable(); // الرقم الاقتصادي يمكن أن يكون فارغًا
            $table->timestamps(); // حقول الوقت (الإنشاء والتحديث)
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuing_licenses');
    }
};
