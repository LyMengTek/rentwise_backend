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
        Schema::create('landlord_floor_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('landlord_id');
            $table->integer('floor');
            $table->integer('total_rooms');
            $table->timestamps();

               // Foreign key constraint
            $table->foreign('landlord_id')->references('id')->on('user_details')->onDelete('cascade');

               // Ensure unique combination of landlord and floor
            $table->unique(['landlord_id', 'floor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landlord_floor_rooms');
    }
};
