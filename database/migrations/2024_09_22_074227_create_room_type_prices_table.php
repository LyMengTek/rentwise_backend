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
        Schema::create('room_type_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('landlord_id'); // Add landlord_id
            $table->string('type'); // Room type identifier (e.g., 1 for Studio, 2 for 1 Bedroom, etc.)
            $table->integer('type_price'); // Price for this room type
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('landlord_id')->references('id')->on('user_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_type_prices');
    }
};
