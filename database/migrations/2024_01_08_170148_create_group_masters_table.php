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
        Schema::create('group_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('groupCode')->nullable();
            $table->string('headName')->nullable();
            $table->enum('type',['Income','Expenditure','Asset','Liability','Trading','Profit and Loss'])->nullable();
            $table->enum('showJournalVoucher',['Yes','No'])->default('No');
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
        Schema::dropIfExists('group_masters');
    }
};
