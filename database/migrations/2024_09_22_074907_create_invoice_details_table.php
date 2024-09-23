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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('user_details');
                $table->integer('room_code'); // Add this line
                $table->foreign('room_code')
                    ->references('room_code')
                    ->on('utility_usages')
                    ->onDelete('cascade'); // Add this line
            $table->decimal('amount_due', 10, 2);
            $table->timestamp('due_date');
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
