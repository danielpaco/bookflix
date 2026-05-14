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
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('subscription_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency')
                ->default('USD');
            $table->enum('status', [
                'pending',
                'paid',
                'failed',
                'refunded'
            ]);
            $table->string('provider');
            $table->string('provider_payment_id')
                ->nullable();
            $table->json('payload')
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};