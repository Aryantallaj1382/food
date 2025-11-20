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
        Schema::create('restaurant_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->constrained('restaurants')->onDelete('cascade');

            $table->boolean('tam_dar_status')->default(false);
            $table->string('tam_dar_text')->nullable();

            $table->boolean('khosh_status')->default(false);
            $table->string('khosh_text')->nullable();

            $table->boolean('first_status')->default(false);
            $table->string('first_text')->nullable();

            $table->boolean('code_status')->default(false);
            $table->string('code_text')->nullable();

            $table->boolean('send_status')->default(false);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_discounts');
    }
};
