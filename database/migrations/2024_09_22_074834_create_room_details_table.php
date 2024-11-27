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
            $table->foreignId('utility_price_id')->constrained('utility_prices')->onDelete('cascade'); // Ensure correct field name
            $table->foreignId('room_type_price_id')->constrained('room_type_prices')->onDelete('cascade'); // Ensure correct field name
            $table->foreignId('user_id')
                ->constrained('user_details')
                ->onDelete('cascade');
            $table->integer('room_code'); // Add this line
            $table->foreign('room_code')
                ->references('room_code')
                ->on('utility_usages')
                ->onDelete('cascade'); // Add this line
            $table->string('room_number');
            $table->text('description');
        
            $table->boolean('available')->default(true);
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
