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
        Schema::create('account_deductions', function (Blueprint $table) {
            $table->id();
            $table->date('depositDate');
            $table->string('bankCode');
            $table->string('memberAccountNo');
            $table->enum('rdAccount',['']);
            $table->enum('rdAmount',['']);
            $table->enum('savingsAccount',['']);
            $table->enum('savingsAmount',['']);
            $table->enum('loanAccount',['']);
            $table->enum('loanAmount',['']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_deductions');
    }
};
