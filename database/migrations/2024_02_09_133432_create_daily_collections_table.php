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
        Schema::create('daily_collections', function (Blueprint $table) {
            $table->id();
            $table->string('serialno')->nullable();
            $table->string('membertype')->nullable();
            $table->string('accountid')->nullable();
            $table->string('accountNo')->nullable();
            $table->string('daily_saving_accno')->nullable();
            $table->date('date');
            $table->string('lockindays')->nullable();
            $table->string('lockindate')->nullable();
            $table->double('amount',20,4)->default(0);
            $table->double('penelty',20,4)->default(0);
            $table->double('interest_amount',20,4)->default(0);
            $table->string('schemeid')->nullable();
            $table->string('schemename')->nullable();
            $table->double('interest',20,4)->default(0);
            $table->integer('month')->nullable();
            $table->string('collectiontype')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('nomineename')->nullable();
            $table->string('nomineerelation')->nullable();
            $table->date('maturitydate');
            $table->string('principalamount')->nullable();
            $table->string('maturityamount')->nullable();
            $table->date('actualMaturitydate')->nullable();
            $table->double('ActualyMaturityAmount',20,4)->default(0);
            $table->enum('PaymentMode',['Cash','Transfer'])->default('Cash');
            $table->enum('status',['Active','Mature','Closed','Deleted'])->default('Active');
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
        Schema::dropIfExists('daily_collections');
    }
};
