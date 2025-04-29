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
        Schema::create('mis', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo')->nullable();
            $table->date('date');
            $table->string('member_type');
            $table->string('account_no');
            $table->string('mis_ac_no')->default(0);
            $table->string('amount');
            $table->integer('interest'); 
            $table->integer('period_year');
            $table->integer('period_month');
            $table->string('TotalInterest');
            $table->string('monthly_interest');
            $table->string('maturity_date');
            $table->string('maturity_amount');
            $table->string('payment_type');
            $table->string('groupCode')->nullable();
            $table->string('ledgerCode')->nullable();
            $table->string('rd_interestROI')->nullable();
            $table->string('rd_interest')->nullable();
            $table->string('interest_deposite');
            $table->string('SavingRdAccountNumber')->nullable();
            $table->enum('status', ['Active', 'Closed', 'Mature'])->default('Active');
            $table->enum('cron_status',['pending','processing','success'])->default('pending');
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
        Schema::dropIfExists('mis');
    }
};
