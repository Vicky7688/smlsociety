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
        Schema::create('contributions', function (Blueprint $table) {
            $table->id()->index();
             $table->string('serialNo')->nullable();
            $table->unsignedBigInteger('accountId')->nullable();
            $table->string('accountNo')->nullable();
            $table->enum('memberType',['Member','NonMember','Staff'])->nullable();
            $table->string('groupCode')->nullable();
            $table->string('ledgerCode')->nullable();
            $table->string('contributionNo')->nullable();
            $table->date('transactionDate')->nullable();
            $table->enum('transactionType',['Deposit','Withdraw'])->nullable();
            $table->double('depositAmount', 20, 4)->default(0);
            $table->double('withdrawAmount', 20, 4)->default(0);
            $table->double('dividendAmount',20,4)->default(0);
            $table->string('chequeNo')->nullable();
            $table->string('narration')->nullable();
            $table->unsignedBigInteger('branchId')->nullable();
            $table->unsignedBigInteger('agentId')->nullable();
            $table->unsignedBigInteger('sessionId')->nullable();
            $table->unsignedBigInteger('updatedBy')->nullable();
            $table->enum('is_delete',['Yes','No'])->default('No');
            $table->enum('txnType',['main','transfer'])->default('main');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
