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
        Schema::create('re_curring_rds', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo')->nullable();
            $table->enum('memberType', ['Member', 'NonMember', 'Staff']);
            $table->foreignId('accountId')->constrained('member_accounts');
            $table->string('accountNo')->nullable();
            $table->string('rd_account_no');
            $table->double('amount', 20, 4);
            $table->string('month')->nullable();
            $table->date('date');
            $table->double('paid_interest', 20, 4)->default(0);
            $table->string('ledger_folio_no')->nullable();
            $table->string('rd_created_from')->nullable();
            $table->integer('misid')->nullable();
            $table->double('bank_deduction', 20, 4)->nullable();
            $table->string('nominee_name')->nullable();
            $table->string('nominee_relation')->nullable();
            $table->string('rd_type')->nullable();
            $table->string('nominee_contact')->nullable();
            $table->longtext('nominee_address')->nullable();
            $table->double('interest', 20, 4)->nullable();
            $table->date('maturity_date')->nullable();
            $table->double('maturity_amount')->nullable();
            $table->enum('status', ['Mature', 'Active', 'Closed', 'Locked'])->default('Active');
            $table->enum('payment_method', ['Cash', 'Transfer'])->nullable();
            $table->date('actual_maturity_date')->nullable();
            $table->double('actual_maturity_amount', 20, 4)->nullable();
            $table->date('status_date')->nullable();
            $table->unsignedBigInteger('agentId')->nullable();
            $table->unsignedBigInteger('branchId')->nullable();
            $table->unsignedBigInteger('sessionId')->nullable();
            $table->unsignedBigInteger('updatedBy')->nullable();
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
        Schema::dropIfExists('re_curring_rds');
    }
};
