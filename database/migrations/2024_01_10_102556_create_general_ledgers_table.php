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
        Schema::create('general_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo')->nullable();
            $table->unsignedBigInteger('accountId')->nullable();
            $table->string('accountNo')->nullable();
            $table->enum('memberType',['Member','NonMember','Staff'])->nullable();
            $table->string('groupCode')->nullable();
            $table->string('ledgerCode')->nullable();
            $table->string('formName')->nullable();
            $table->unsignedBigInteger('referenceNo')->nullable();
            $table->enum('entryMode',['manual','automatic'])->default('manual');
            $table->date('transactionDate')->nullable();
            $table->enum('transactionType',['Dr','Cr'])->nullable();
            $table->double('transactionAmount',20,4)->default(0);
            $table->string('narration')->nullable();
            $table->unsignedBigInteger('branchId')->nullable();
            $table->unsignedBigInteger('agentId')->nullable();
            $table->unsignedBigInteger('sessionId')->nullable();
            $table->unsignedBigInteger('updatedBy')->nullable();
            $table->enum('is_delete',['Yes','No'])->default('No');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_ledgers');
    }
};
