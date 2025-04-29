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
        Schema::create('transfered_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accountId')->nullable();
            $table->enum('accountType',['Single','Joint','UnderGuardian'])->nullable();
            $table->enum('memberType',['Member','NonMember','Staff'])->nullable();
            $table->string('accountNo')->nullable();
            $table->string('guardianAccountNo')->nullable();
            $table->string('name')->nullable();
            $table->string('fatherName')->nullable();
            $table->date('birthDate')->nullable();
            $table->enum("gender",["Male","Female"])->default("Male");
            $table->string('caste')->nullable();
            $table->string('aadharNo')->nullable();
            $table->string('panNo')->nullable();
            $table->string('occupation')->nullable();
            $table->string('employeeCode')->nullable();
            $table->string('ledgerNo')->nullable();
            $table->string('pageNo')->nullable();
            $table->string('idProof')->nullable();
            $table->string('photo')->nullable();
            $table->string('signature')->nullable();
            $table->unsignedBigInteger('agentId')->nullable();

            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('tehsil')->nullable();
            $table->string('postOffice')->nullable();
            $table->string('village')->nullable();
            $table->string('wardNo')->nullable();
            $table->string('address')->nullable();
            $table->integer('pincode')->nullable();
            $table->bigInteger('phone')->nullable();

            $table->string('nomineeName')->nullable();
            $table->string('nomineeRelation')->nullable();
            $table->date('nomineeBirthDate')->nullable();
            $table->bigInteger('nomineePhone')->nullable();
            $table->string('nomineeAddress')->nullable();

            $table->string('transferReason')->nullable();
            $table->date('transferDate')->nullable();
            $table->date('closingDate')->nullable();
            $table->unsignedBigInteger('branchId')->nullable();
            $table->unsignedBigInteger('sessionId')->nullable();
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
        Schema::dropIfExists('transfered_accounts');
    }
};
