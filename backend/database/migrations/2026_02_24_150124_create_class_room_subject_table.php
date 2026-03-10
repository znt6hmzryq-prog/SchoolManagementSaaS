<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_room_subject', function (Blueprint $table) {
            $table->id();

            $table->foreignId('class_room_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('subject_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->timestamps();

            // Prevent duplicate assignment
            $table->unique(['class_room_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_room_subject');
    }
};