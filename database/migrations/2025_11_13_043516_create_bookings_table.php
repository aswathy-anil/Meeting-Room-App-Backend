<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->dateTime('from_datetime');
            $table->dateTime('to_datetime');
            $table->enum('status', ['booked', 'confirmed', 'free', 'rejected'])->default('booked');
            $table->timestamps();

            // Prevent overlapping bookings for the same room
            $table->unique(['room_id', 'from_datetime', 'to_datetime'], 'unique_room_booking');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
