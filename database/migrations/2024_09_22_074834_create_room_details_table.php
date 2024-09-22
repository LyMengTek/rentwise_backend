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
        Schema::create('room_details', function (Blueprint $table) {
            $table->id();
            $table->integer('floor');
            $table->foreignId('user_id')
            ->constrained('user_details')
            ->onDelete('cascade');
            $table->string('room_number');
            $table->decimal('water_price', 10, 2);
            $table->decimal('electricity_price', 10, 2);
            $table->boolean('available')->default(true);
            $table->decimal('room_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_details');
    }
};
