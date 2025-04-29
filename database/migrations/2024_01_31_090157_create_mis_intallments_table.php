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
        Schema::create('mis_intallments', function (Blueprint $table) {
            $table->id();
            $table->string("serialNo")->nullable();
            $table->foreignId('mis_id')->constrained('mis');
            $table->date('installment_date');
            $table->string('installment_amount');
            $table->integer('installment_no');
            $table->date('receipt_date')->nullable();
            $table->enum('type',['RD','Saving','Loan'])->nullable();
            $table->enum('status',['pending','paid'])->default('pending');
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
        Schema::dropIfExists('mis_intallments');
    }
};
