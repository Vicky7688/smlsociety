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
        Schema::create('ledger_masters', function (Blueprint $table) {
            $table->id();
            $table->string('groupCode')->nullable();
            $table->string('name')->nullable();
            $table->string('ledgerCode')->nullable();
            $table->double('openingAmount',20,4)->nullable();
            $table->enum('openingType',['Dr','Cr'])->nullable();
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
        Schema::dropIfExists('ledger_masters');
    }
};
