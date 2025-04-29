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
        Schema::create('rd_installments', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo')->nullable();
            $table->foreignId('rd_id')->constrained('re_curring_rds');
            $table->date('installment_date');
            $table->double('amount',20,4)->default(0);
            $table->date('payment_date')->nullable();
            $table->double('panelty',20,4)->default(0);
            $table->double('paid_amount',20,4)->default(0);
            $table->integer('intallment_no')->nullable();
            $table->enum('payment_status',['pending','paid'])->default('pending');
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
        Schema::dropIfExists('rd_installments');
    }
};
