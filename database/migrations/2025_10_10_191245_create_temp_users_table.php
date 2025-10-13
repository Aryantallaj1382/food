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
        Schema::create('temp_users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique()->nullable();
            $table->unsignedBigInteger('mobile')->unique()->nullable();
            $table->string('sms_sent_code')->nullable();
            $table->timestamp('sms_sent_date')->nullable();
            $table->string('sms_sent_tries')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_users');
    }
};
