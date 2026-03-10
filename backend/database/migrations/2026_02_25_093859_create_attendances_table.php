<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Tenant isolation
            $table->foreignId('school_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Academic reference
            $table->foreignId('teaching_assignment_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('student_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->enum('status', [
                'present',
                'absent',
                'late',
                'excused'
            ]);

            $table->timestamps();

            // Prevent duplicate attendance per student per assignment per day
            $table->unique([
                'school_id',
                'teaching_assignment_id',
                'student_id',
                'attendance_date'
            ], 'attendance_unique_constraint');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};