<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('invoice_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('amount_paid', 12, 2);

            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'card',
                'mobile_money',
                'online'
            ]);

            $table->string('transaction_reference')
                  ->nullable();

            $table->timestamp('paid_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};