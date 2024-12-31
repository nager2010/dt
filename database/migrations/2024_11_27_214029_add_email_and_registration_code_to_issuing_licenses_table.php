<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailAndRegistrationCodeToIssuingLicensesTable extends Migration
{
    public function up()
    {
        Schema::table('issuing_licenses', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phoneNumber'); // إضافة حقل البريد الإلكتروني
            $table->json('registrationCode')->nullable()->after('email'); // إضافة حقل رمز التسجيل كـ JSON
        });
    }

    public function down()
    {
        Schema::table('issuing_licenses', function (Blueprint $table) {
            $table->dropColumn('email'); // إزالة الحقل عند التراجع
            $table->dropColumn('registrationCode'); // إزالة الحقل عند التراجع
        });
    }
}
