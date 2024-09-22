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
            $table->foreignId('landlord_id')
                ->constrained('landlord_details')
            ->onDelete('cascade');
            $table->foreignId('room_type_id')
                ->constrained('roomtype_details')
            ->onDelete('cascade');
            $table->string('room_number');
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
