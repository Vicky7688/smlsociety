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
        Schema::create('journal_voucher_details', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo');
            $table->unsignedBigInteger('voucherId')->nullable();
            $table->string('groupCode')->nullable();
            $table->string('ledgerCode')->nullable();
            $table->enum('transactionType',['Dr','Cr'])->nullable();
            $table->double('drAmount',20,4)->nullable();
            $table->double('crAmount',20,4)->nullable();
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
        Schema::dropIfExists('journal_voucher_details');
    }
};
