<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // تحديث القيم المنتهية إلى قيمة مؤقتة
        DB::table('issuing_licenses')
            ->where('status', 'منتهية')
            ->update(['status' => 'temp_expired']);

        // تحديث القيم السارية إلى منتهية
        DB::table('issuing_licenses')
            ->where('status', 'سارية')
            ->update(['status' => 'منتهية']);

        // تحديث القيم المؤقتة إلى سارية
        DB::table('issuing_licenses')
            ->where('status', 'temp_expired')
            ->update(['status' => 'سارية']);
    }

    public function down()
    {
        // تحديث القيم السارية إلى قيمة مؤقتة
        DB::table('issuing_licenses')
            ->where('status', 'سارية')
            ->update(['status' => 'temp_active']);

        // تحديث القيم المنتهية إلى سارية
        DB::table('issuing_licenses')
            ->where('status', 'منتهية')
            ->update(['status' => 'سارية']);

        // تحديث القيم المؤقتة إلى منتهية
        DB::table('issuing_licenses')
            ->where('status', 'temp_active')
            ->update(['status' => 'منتهية']);
    }
};
