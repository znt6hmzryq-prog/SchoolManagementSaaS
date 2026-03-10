<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            // Tenant isolation
            $table->foreignId('school_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Link to assessment
            $table->foreignId('assessment_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('student_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('score', 8, 2);

            $table->timestamps();

            // Prevent duplicate grade per student per assessment
            $table->unique([
                'school_id',
                'assessment_id',
                'student_id'
            ], 'grade_unique_constraint');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};