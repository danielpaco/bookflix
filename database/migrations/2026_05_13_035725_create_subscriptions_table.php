<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {

            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('plan_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->timestamp('grace_ends_at')
                ->nullable();
            $table->enum('status', [
                'active',
                'expired',
                'cancelled',
                'grace'
            ])->default('active');
            $table->string('provider')
                ->nullable();
            $table->string('provider_subscription_id')
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};