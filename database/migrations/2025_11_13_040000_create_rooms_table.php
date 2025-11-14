<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('capacity');
            $table->string('location');
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->time('start_time')->nullable(); // optional: available from
            $table->time('end_time')->nullable();   // optional: available till
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
