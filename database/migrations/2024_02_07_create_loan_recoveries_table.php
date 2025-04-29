<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('loan_recoveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loanId');
            $table->date('receiptDate');
            $table->string('principal');
            $table->string('interest');
            $table->string('overDueInterest')->default(0);
            $table->string('pendingInterest');
            $table->string('penalInterest');
            $table->string('total');
            $table->string('receivedAmount');
            $table->enum('receivedBy', ['Cash', 'Bank']);
            $table->enum('status', ['True', 'False'])->default('True');
            $table->enum('entry_mode', ['manual', 'automatic'])->default('manual');
            $table->datetime('deleted_date')->nullable()->default(null);
            $table->integer('branchId');
            $table->string('instaId')->nullable()->default(null);
            $table->integer('sessionId');
            $table->integer('updatedBy');
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
        Schema::dropIfExists('loan_recoveries');
    }
};
