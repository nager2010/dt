<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // حذف البيانات القديمة
        DB::table('license_types')->truncate();

        // إضافة البيانات الجديدة
        $sql = "INSERT INTO license_types (name, code, fee) VALUES
            ('رخص محلات تسويق المنتجات الثلجية', '101010100', 65.000),
            ('رخص محلات تسويق المنتجات الثلجية بالجملة', '101010200', 85.000),
            ('رخص محلات بيع معدات الصيد البحري', '101010300', 65.000),
            ('رخص محلات بيع معدات الصيد البحري بالجملة', '101010400', 85.000),
            ('رخص محلات بيع الدرجات الهوائية والنارية', '101010500', 85.000),
            ('رخص محلات بيع الدرجات الهوائية والنارية بالجملة', '101010800', 110.000),
            ('رخص محلات بيع لحوم الدواجن', '101010700', 100.000),
            ('رخص محلات بيع الاسماك', '101010800', 85.000),
            ('رخص محلات بيع الحلويات', '101010900', 65.000),
            ('رخص محلات بيع التحف والهدايا', '101011000', 100.000),
            ('رخص محلات بيع مستلزمات العناية بالجسم', '101011100', 85.000),
            ('رخص محلات بيع مستلزمات العناية بالجسم بالجملة', '101011200', 100.000),
            ('رخص محلات بيع الطيور والحيوانات المنزلية', '101011300', 85.000),
            ('رخص محلات بيع الموازين وأجهزة القياس', '101011400', 110.000),
            ('رخص محلات بيع التوابل والبقوليات', '101011500', 65.000),
            ('رخص محلات بيع التوابل والبقوليات بالجملة', '101011600', 110.000),
            ('رخص محلات بيع مستلزمات الأم والطفل', '101011700', 100.000),
            ('رخص محلات بيع مستلزمات الأم والطفل بالجملة', '101011800', 125.000),
            ('رخص محلات بيع مستلزمات المخابز والحلويات', '101011900', 85.000),
            ('رخص محلات بيع مستلزمات المخابز والحلويات بالجملة', '101012000', 125.000)";
            
        DB::unprepared($sql);
    }

    public function down()
    {
        DB::table('license_types')->truncate();
    }
};
