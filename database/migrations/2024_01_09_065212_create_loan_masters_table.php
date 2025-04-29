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
        Schema::create('loan_masters', function (Blueprint $table) {
            $table->id();
            $table->enum('memberType', ['Member', 'NonMember', 'Staff'])->nullable();
            $table->string('loanType')->nullable();

            $table->string('name')->nullable();
            $table->double('processingFee', 20, 4)->nullable();
            $table->integer('years')->nullable();
            $table->integer('months')->nullable();
            $table->integer('days')->nullable();
            $table->double('interest', 20, 4)->nullable();
            $table->double('penaltyInterest', 20, 4)->nullable();
            $table->integer('emiDate')->nullable();
            $table->string('insType')->nullable();
            $table->enum('advancementDate', ['Yes', 'No'])->default('Yes');
            $table->enum('recoveryDate', ['Yes', 'No'])->default('Yes');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->unsignedBigInteger('updatedBy')->nullable();
            $table->enum('is_delete', ['Yes', 'No'])->default('No');
            $table->integer('ledger_master_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_masters');
    }
};
