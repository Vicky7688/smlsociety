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
        Schema::create('daily_loans', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('membertype');
            $table->string('accountID');
            $table->string('accountNo');
            $table->string('dailyloanaccno');
            $table->enum('status',['Active','Closed'])->default('Active');
            $table->date('statusdate')->nullable();
            $table->enum('is_delete',['Yes','No'])->default('No');
            $table->string('deleted_by')->nullable();
            $table->date('delete_date')->nullable();
            $table->integer('branch')->nullable();
            $table->integer('loginid')->nullable();
            $table->string('session_year')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_loans');
    }
};
