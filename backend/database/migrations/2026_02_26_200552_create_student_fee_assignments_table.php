<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fee_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('student_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('fee_structure_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Allows custom override amount (scholarship / discount)
            $table->decimal('custom_amount', 12, 2)
                  ->nullable();

            $table->boolean('is_active')
                  ->default(true);

            $table->timestamps();

            $table->unique([
                'student_id',
                'fee_structure_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fee_assignments');
    }
};