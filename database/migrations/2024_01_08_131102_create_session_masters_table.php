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
        Schema::create('session_masters', function (Blueprint $table) {
            $table->id();
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->enum('status',['Active','Inactive','Closed'])->default('Active');
            $table->enum('auditPerformed',['Yes','No'])->default('No');
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
        Schema::dropIfExists('session_masters');
    }
};
