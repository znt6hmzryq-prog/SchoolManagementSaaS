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
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('school_id')
              ->nullable()
              ->constrained()
              ->cascadeOnDelete();

        $table->enum('role', [
            'super_admin',
            'school_admin',
            'teacher',
            'student',
            'parent'
        ])->default('school_admin');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropConstrainedForeignId('school_id');
        $table->dropColumn('role');
    });
}
};
