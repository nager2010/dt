<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValueToNameInMunicipalitiesTable extends Migration
{
    public function up()
    {
        Schema::table('municipalities', function (Blueprint $table) {
            $table->string('name')->default('Unnamed Municipality')->change();
        });
    }

    public function down()
    {
        Schema::table('municipalities', function (Blueprint $table) {
            $table->string('name')->default(null)->change();
        });
    }
}
