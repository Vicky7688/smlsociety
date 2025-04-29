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
        Schema::create('borrowing_limit_masters', function (Blueprint $table) {
            $table->id();
            $table->double('multiplyValue',20,4)->nullable();
            $table->double('percentageValue',20,4)->nullable();
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
        Schema::dropIfExists('borrowing_limit_masters');
    }
};
