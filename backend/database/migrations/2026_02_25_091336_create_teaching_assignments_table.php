<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teaching_assignments', function (Blueprint $table) {
            $table->id();

            // Tenant safety
            $table->foreignId('school_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Academic structure
            $table->foreignId('academic_year_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('class_room_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('subject_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->timestamps();

            // Prevent duplicate teaching assignment
            $table->unique([
                'school_id',
                'academic_year_id',
                'class_room_id',
                'subject_id',
                'teacher_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_assignments');
    }
};