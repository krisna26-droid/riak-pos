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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('cashier_shift_id')->constrained('cashier_shifts')->cascadeOnDelete();
            $table->string('table_number', 50)->nullable();
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('tax')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('grand_total');
            $table->enum('payment_method', ['cash', 'qris', 'transfer', 'later']);
            $table->unsignedInteger('cash_received')->default(0);
            $table->unsignedInteger('cash_change')->default(0);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('paid');
            $table->string('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};