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
        Schema::create('member_loans', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo');
            $table->integer('accountId');
            $table->bigInteger('accountNo');
            $table->date('loanDate');
            $table->date('loanEndDate');
            $table->string('memberType');
            $table->string('groupCode');
            $table->string('ledgerCode');
            $table->string('loanAcNo', 500)->default('0');
            $table->string('purpose');
            $table->integer('loanType');
            $table->float('processingFee');
            $table->float('processingRates');
            $table->enum('cropType', ['Cash', 'Kind', 'Ancillary', 'Diesel'])->default('Cash');
            $table->integer('cropMasterId')->default(0);
            $table->string('invoiceNumber', 500)->default('0');
            $table->string('loanYear');
            $table->string('loanMonth');
            $table->string('loanInterest');
            $table->string('loanPanelty');
            $table->string('fdId')->nullable();
            $table->string('fdAmount')->nullable();
            $table->string('rd_id')->nullable();
            $table->string('rd_aacount')->nullable();
            $table->string('loanAmount');
            $table->string('bankDeduction', 10);
            $table->string('deductionAmount');
            $table->string('pernote');
            $table->string('loanBy');
            $table->integer('ledgerBankAccountId')->default(0);
            $table->string('chequeNo');
            $table->string('installmentType');
            $table->string('guranter1')->nullable();;
            $table->string('guranter2')->nullable();;
            $table->string('agentId')->nullable();;
            $table->enum('status', ['Disbursed', 'Closed', 'Inactive'])->default('Disbursed');
            $table->integer('branchId');
            $table->integer('sessionId');
            $table->integer('updatedBy');
            $table->enum('is_delete', ['Yes', 'No'])->default('No');
            $table->softDeletes();
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_loans');
    }
};
