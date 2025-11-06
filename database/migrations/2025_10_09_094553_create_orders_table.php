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
        Schema::create('ordersController', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('total_amount')->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('sending_method', ['pike', 'in_person'])->nullable()->default('pike');
            $table->enum('payment_method', ['online', 'cash'])->nullable()->default('online');
            $table->enum('gateway', ['melat', 'zarinpal'])->nullable();
            $table->enum('status', ['processing', 'completed', 'cancelled'])->default('processing');
            $table->string('discount_code')->nullable();
            $table->boolean('restaurant_discount')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->text('notes')->nullable();
            $table->string('time')->nullable();

            $table->timestamps();
        });




}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordersController');
    }
};
