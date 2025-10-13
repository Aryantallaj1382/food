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
        Schema::create('food_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_id')->constrained()->onDelete('cascade');
            $table->string('name'); // مثل: مینی، متوسط، بزرگ
            $table->decimal('price', 10, 2);
            $table->decimal('price_discount', 10, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
        Schema::rename('food', 'foods');

        Schema::table('foods', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('price_discount');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_options');
    }
};
