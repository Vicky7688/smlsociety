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
        Schema::create('member_fds', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo')->nullable();
            $table->unsignedBigInteger('accountId')->nullable();
            $table->string('accountNo')->nullable();
            $table->enum('memberType', ['Member', 'NonMember', 'Staff'])->nullable();
            $table->string('groupCode')->nullable();
            $table->string('ledgerCode')->nullable();

            $table->string('fdNo')->nullable();
            $table->enum('fdType', ['FixedDeposit', 'LongTermDeposit', 'SahakaritaBond', 'SpecialBachatYojnaBond'])->nullable();
            $table->date('openingDate')->nullable();
            $table->double('principalAmount', 20, 4)->nullable();
            $table->enum('interestType', ['Fixed', 'AnnualCompounded', 'QuarterlyCompounded'])->nullable();
            $table->date('interestStartDate')->nullable();
            $table->double('interestRate', 20, 4)->nullable();
            $table->double('interestAmount', 20, 4)->nullable();
            $table->integer('years')->nullable();
            $table->integer('months')->nullable();
            $table->integer('days')->nullable();
            $table->date('maturityDate')->nullable();
            $table->date('onmaturityDate')->nullable();
            $table->double('maturityAmount', 20, 4)->nullable();
            $table->date('actualMaturityDate')->nullable();
            $table->double('actualInterestAmount', 20, 4)->nullable();
            $table->double('actualMaturityAmount', 20, 4)->nullable();
            $table->string('ledgerNo')->nullable();
            $table->string('pageNo')->nullable();
            $table->string('narration')->nullable();

            $table->string('transferedFrom')->nullable();
            $table->string('paymentType')->nullable();
            $table->string('bank')->nullable();
            $table->string('chequeNo')->nullable();
            $table->string('transferedTo')->nullable();
            $table->string('transferedPaymentType')->nullable();
            $table->string('transferedBank')->nullable();
            $table->string('transferedChequeNo')->nullable();

            $table->string('nomineeName1')->nullable();
            $table->string('nomineeRelation1')->nullable();
            $table->date('nomineeBirthDate1')->nullable();
            $table->bigInteger('nomineePhone1')->nullable();
            $table->string('nomineeAddress1')->nullable();
            $table->string('nomineeName2')->nullable();
            $table->string('nomineeRelation2')->nullable();
            $table->date('nomineeBirthDate2')->nullable();
            $table->bigInteger('nomineePhone2')->nullable();
            $table->string('nomineeAddress2')->nullable();

            $table->date('renewDate')->nullable();
            $table->string('oldFdNo')->nullable();
            $table->enum('status', ['Active', 'Matured', 'Renewed', 'Locked'])->nullable();
            $table->unsignedBigInteger('agentId')->nullable();
            $table->unsignedBigInteger('branchId')->nullable();
            $table->unsignedBigInteger('sessionId')->nullable();
            $table->unsignedBigInteger('updatedBy')->nullable();
            $table->enum('is_delete', ['Yes', 'No'])->default('No');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_fds');
    }
};
