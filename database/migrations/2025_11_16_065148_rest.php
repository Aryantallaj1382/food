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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->boolean('tax_enabled')->default(false);
            $table->boolean('panel_editable')->default(false);
            $table->string('distance_km')->nullable();
            $table->string('cost_per_km')->nullable();
            $table->boolean('free_shipping')->default(false);
            $table->enum('cod_courier', ['restaurant_courier', 'ghazaresan'])->nullable();
            $table->enum('online_courier', ['restaurant_courier', 'ghazaresan'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
