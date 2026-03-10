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
        Schema::table('schools', function (Blueprint $table) {

            // Subscription expiry date
            $table->timestamp('subscription_expires_at')
                  ->nullable()
                  ->after('subscription_status');

            // Plan limits
            $table->integer('max_students')
                  ->default(50)
                  ->after('subscription_expires_at');

            $table->integer('max_teachers')
                  ->default(10)
                  ->after('max_students');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {

            $table->dropColumn([
                'subscription_expires_at',
                'max_students',
                'max_teachers'
            ]);
        });
    }
};