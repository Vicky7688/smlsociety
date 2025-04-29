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
        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('LoanId');
            $table->date('installmentDate');
            $table->string('principal');
            $table->string('interest');
            $table->string('total');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['True', '', 'False', 'Partial'])->default('False');
            $table->unsignedBigInteger('re_amount')->default('0');
            $table->timestamps();
            $table->softDeletes();
            $table->index('loanId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_installments');
    }
};
