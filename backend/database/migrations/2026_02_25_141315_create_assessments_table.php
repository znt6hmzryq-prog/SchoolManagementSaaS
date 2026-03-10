<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();

            // Tenant isolation
            $table->foreignId('school_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Link to teaching context
            $table->foreignId('teaching_assignment_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Assessment details
            $table->string('title'); // e.g. Midterm Exam
            $table->enum('type', [
                'quiz',
                'homework',
                'midterm',
                'final',
                'project',
                'assignment'
            ]);

            $table->decimal('max_score', 8, 2);
            $table->decimal('weight', 5, 2)->default(1); 
            // weight used later for weighted average

            $table->date('assessment_date');

            $table->timestamps();

            // Prevent duplicate title for same assignment on same date
            $table->unique([
                'school_id',
                'teaching_assignment_id',
                'title',
                'assessment_date'
            ], 'assessment_unique_constraint');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};