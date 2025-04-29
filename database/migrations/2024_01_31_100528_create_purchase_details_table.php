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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoiceId')->nullable();
            $table->string('itemCode')->nullable();
            $table->string('itemName')->nullable();
            $table->string('itemUnit')->nullable();
            $table->double('quantity',20,4)->nullable();
            $table->double('price',20,4)->nullable();
            $table->double('cess',20,4)->nullable();
            $table->double('igst',20,4)->nullable();
            $table->double('cgst',20,4)->nullable();
            $table->double('sgst',20,4)->nullable();
            $table->double('subTotal',20,4)->nullable();
            $table->double('grandTotal',20,4)->nullable();
            $table->unsignedBigInteger('branchId')->nullable();
            $table->unsignedBigInteger('sessionId')->nullable();
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
        Schema::dropIfExists('purchase_details');
    }
};
