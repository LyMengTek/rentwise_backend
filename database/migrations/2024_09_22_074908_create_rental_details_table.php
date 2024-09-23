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
        Schema::create('rental_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landord_id')
                ->constrained('user_details')
                ->onDelete('cascade');
            $table->foreignId('renter_id')
                ->constrained('user_details')
                ->onDelete('cascade');
            $table->foreignId('room_id')
                ->constrained('room_details')
                ->onDelete('cascade');
            $table->foreignId('invoice_id')
                ->constrained('invoice_details')
                ->onDelete('cascade');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_details');
    }
};
