<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // If assigned to a class
            $table->foreignId('class_room_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // If assigned directly to student
            $table->foreignId('student_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('name'); // Tuition, Exam, Transport

            $table->decimal('amount', 12, 2);

            $table->enum('frequency', [
                'one_time',
                'monthly',
                'quarterly',
                'yearly'
            ]);

            $table->unsignedTinyInteger('due_day')->nullable(); 
            // for monthly recurring (1–31)

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};