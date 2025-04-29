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
        Schema::create('joint_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accountId')->nullable();
            $table->string('name')->nullable();
            $table->string('fatherName')->nullable();
            $table->date('birthDate')->nullable();
            $table->enum("gender",["Male","Female"])->default("Male");
            $table->string('caste')->nullable();
            $table->string('aadharNo')->nullable();
            $table->string('panNo')->nullable();
            $table->string('occupation')->nullable();
            $table->string('employeeCode')->nullable();
            $table->string('idProof')->nullable();
            $table->string('photo')->nullable();
            $table->string('signature')->nullable();

            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('tehsil')->nullable();
            $table->string('postOffice')->nullable();
            $table->string('village')->nullable();
            $table->string('wardNo')->nullable();
            $table->string('address')->nullable();
            $table->integer('pincode')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->unsignedBigInteger('updatedBy')->nullable();
            $table->enum('is_delete',['Yes','No'])->default('No');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joint_accounts');
    }
};
