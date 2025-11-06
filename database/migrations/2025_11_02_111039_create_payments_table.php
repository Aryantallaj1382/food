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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // سفارش مربوطه
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');   // کاربر پرداخت کننده

            $table->string('amount');         // مبلغ پرداخت
            $table->string('payment_method');         // روش پرداخت (wallet, gateway, cash ...)
            $table->string('gateway')->nullable();    // درگاه پرداخت (در صورت وجود)
            $table->string('transaction_id')->nullable();    // درگاه پرداخت (در صورت وجود)
            $table->string('status')->default('pending'); // وضعیت پرداخت: pending, completed, failed

            $table->text('notes')->nullable();        // توضیحات اختیاری

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
