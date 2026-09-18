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
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->unsignedInteger('starting_cash'); // Modal awal kasir
            $table->unsignedInteger('expected_ending_cash')->nullable(); // Total kalkulasi sistem
            $table->unsignedInteger('actual_ending_cash')->nullable(); // Fisik uang saat tutup
            $table->integer('difference')->nullable(); // Selisih kas (plus/minus)
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
