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
        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();

            // School Reference
            $table->foreignId('school_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Parent (User with role = parent)
            $table->foreignId('parent_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Student Reference
            $table->foreignId('student_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->timestamps();

            // Prevent duplicate parent-student assignment
            $table->unique(['parent_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_student');
    }
};