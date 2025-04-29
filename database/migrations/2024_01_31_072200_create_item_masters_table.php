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
        Schema::create('item_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->enum('type',['Control','NonControl','Fertilizer'])->nullable();
            $table->string('unit')->nullable();
            $table->double('purchaseRate',20,4)->nullable();
            $table->double('saleRate',20,4)->nullable();
            $table->unsignedBigInteger('taxId')->nullable();
            $table->unsignedBigInteger('purchaseTax')->nullable();
            $table->unsignedBigInteger('saleTax')->nullable();
            $table->integer('openingStock')->nullable();
            $table->integer('reorderLevel')->nullable();
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
        Schema::dropIfExists('item_masters');
    }
};
