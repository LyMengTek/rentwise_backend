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
            $table->foreignId('utility_price_id')->nullable()->constrained('utility_prices')->onDelete('cascade');
            $table->foreignId('room_type_price_id')->nullable()->constrained('room_type_prices')->onDelete('cascade'); // Make nullable
            $table->foreignId('user_id')->nullable()->constrained('user_details')->onDelete('cascade');
            $table->string('room_number')->unique();
            $table->text('description');
            $table->boolean('available')->default(true);
            $table->timestamps();
        
            // Optional addition to link utilities
            $table->integer('room_code')->unique(); 
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
