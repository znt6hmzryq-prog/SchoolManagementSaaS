<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();

            // Tenant safety
            $table->foreignId('school_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Link to academic year
            $table->foreignId('academic_year_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('name');
            $table->string('code')->nullable();

            $table->timestamps();

            // Prevent duplicate subject names in same year + school
            $table->unique(['school_id', 'academic_year_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};