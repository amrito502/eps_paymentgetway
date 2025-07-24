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
        Schema::create('eps_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('username');
            $table->string('trx_id')->unique();
            $table->string('amount');
            $table->string('status')->default('pending'); // pending, success, failed
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eps_transactions');
    }
};
