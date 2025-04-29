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
        Schema::create('daily_collectionsavings', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo')->nullable();
            $table->integer('dailyaccountid');
            $table->double('balence',20,4)->default(0);
            $table->double('recovery',20,4)->default(0);
            $table->date('receipt_date');
            $table->double('deposit',20,4)->default(0);
            $table->double('withdrow',20,4)->default(0);
            $table->double('interest',20,4)->default(0);
            $table->double('penalty',20,4)->default(0);
            $table->double('maturity_amount',20,4)->default(0);
            $table->string('type')->nullable();
            $table->enum('payment_mode',['Cash','Cheque'])->default('Cash');
            $table->string('bank_name')->nullable();
            $table->string('cheque_no')->nullable();
            $table->string('groupcode')->nullable();
            $table->string('ledgercode')->nullable();
            $table->integer('branchId');
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
        Schema::dropIfExists('daily_collectionsavings');
    }
};
