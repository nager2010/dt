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
        Schema::create('license_requests', function (Blueprint $table) {
            $table->id();
            $table->string('fullName', 120);
            $table->string('nationalID', 20);
            $table->string('passportOrID', 20)->nullable();
            $table->string('phoneNumber', 15);
            $table->string('email', 120)->nullable();
            $table->string('registrationCode', 9);
            $table->string('projectName', 120);
            $table->string('positionInProject');
            $table->string('projectAddress', 160)->nullable();
            $table->unsignedBigInteger('municipality_id');
            $table->unsignedBigInteger('region_id');
            $table->string('license_type_id');
            $table->string('nearestLandmark', 180)->nullable();
            $table->decimal('licenseFee', 10, 2)->nullable();
            $table->integer('licenseDuration')->nullable();
            $table->date('licenseDate')->nullable();
            $table->string('chamberOfCommerceNumber', 20)->nullable();
            $table->string('taxNumber', 20)->nullable();
            $table->string('economicNumber', 20)->nullable();
            $table->text('admin_note')->nullable(); // ملاحظات المدير
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();

            $table->foreign('municipality_id')->references('id')->on('municipalities')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_requests');
    }
};
