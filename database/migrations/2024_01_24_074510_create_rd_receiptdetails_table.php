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
        Schema::create('rd_receiptdetails', function (Blueprint $table) {
            $table->id();
            $table->enum('memberType',['Member','NonMember','Staff']);
            $table->string('serialNo')->nullable();
            $table->double('amount',20,4);
            $table->date('payment_date');
            $table->string('rc_account_no')->nullable();
            $table->string('rd_account_no')->nullable();
            $table->date('installment_date');
            $table->double('panelty',20,4);
            $table->string('mis_id');
            $table->string('narration')->nullable();
            $table->enum('entry_mode',['manual','automatic'])->default('manual');
            $table->enum('is_delete', ['Yes', 'No'])->default('No');
            $table->foreignId('sessionId')->constrained('session_masters')->nullable();;
            $table->foreignId('updatedBy')->constrained('users')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rd_receiptdetails');
    }
};
