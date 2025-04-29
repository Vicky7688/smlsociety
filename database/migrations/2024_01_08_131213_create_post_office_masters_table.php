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
        Schema::create('post_office_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stateId')->nullable();
            $table->unsignedBigInteger('districtId')->nullable();
            $table->unsignedBigInteger('tehsilId')->nullable();
            $table->integer('pincode');
            $table->string('name')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
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
        Schema::dropIfExists('post_office_masters');
    }
};
