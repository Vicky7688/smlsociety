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
        Schema::create('commission_masters', function (Blueprint $table) {
            $table->id();
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->double('commissionSaving',20,4)->nullable();
            $table->double('commissionFD',20,4)->nullable();
            $table->double('commissionRD',20,4)->nullable();
            $table->double('commissionShare',20,4)->nullable();
            $table->double('commissionLoan',20,4)->nullable();
            $table->double('commissionDailyCollection',20,4)->nullable();
            $table->enum('status',['Active','Inactive'])->default('Active');
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
        Schema::dropIfExists('commission_masters');
    }
};
