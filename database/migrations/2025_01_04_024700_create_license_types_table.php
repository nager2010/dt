<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('license_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('fee', 10, 3);
            $table->timestamps();
        });

        // قراءة محتوى ملف SQL وتنفيذه
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
            ('رخص محلات بيع التحف والهدايا', '101011000', 100.000)";
            
        DB::unprepared($sql);
    }

    public function down()
    {
        Schema::dropIfExists('license_types');
    }
};
