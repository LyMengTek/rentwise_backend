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
        if (!Schema::hasTable('utility_usages')) {
        Schema::create('utility_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained('rental_details')->onDelete('cascade'); // Add rental_id foreign key
            $table->integer('room_code')->nullable()->index();
            $table->decimal('water_usage', 10, 2);  // Assuming 2 decimal places
            $table->decimal('electricity_usage', 10, 2);
            $table->decimal('other', 10, 2);
            $table->timestamps();
        });}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('current_utility_usages');
    }
};
