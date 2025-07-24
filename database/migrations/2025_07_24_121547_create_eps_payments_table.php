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
        Schema::create('eps_payments', function (Blueprint $table) {
         $table->id();
        $table->string('invoice_id')->unique();
        $table->decimal('amount', 10, 2);
        $table->string('status')->default('pending'); // pending, success, failed
        $table->json('response')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eps_payments');
    }
};
