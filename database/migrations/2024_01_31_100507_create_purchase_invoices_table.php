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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo');
            $table->date('invoiceDate');
            $table->string('invoiceNo');
            $table->unsignedBigInteger('purchaseClient');
            $table->unsignedBigInteger('depot');
            $table->enum('type',['Control','NonControl','Fertilizer'])->nullable();
            $table->string('paymentType');
            $table->string('bank');
            $table->double('subTotal',20,4);
            $table->double('cess',20,4);
            $table->double('igst',20,4);
            $table->double('cgst',20,4);
            $table->double('sgst',20,4);
            $table->double('freight',20,4);
            $table->double('labour',20,4);
            $table->double('commission',20,4);
            $table->double('discount',20,4);
            $table->double('grandTotal',20,4);
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
        Schema::dropIfExists('purchase_invoices');
    }
};
