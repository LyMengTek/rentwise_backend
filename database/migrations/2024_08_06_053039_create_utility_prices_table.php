<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_prices', function (Blueprint $table) {
            $table->id();
            $table->decimal('water_price', 8, 2);
            $table->decimal('electricity_price', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_prices');
    }
};
