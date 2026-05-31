<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_book_progress', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('last_page')->default(0);

            $table->unsignedInteger('pages_read')->default(0);

            $table->decimal('progress_percent', 5, 2)
                ->default(0);

            $table->unsignedBigInteger('reading_time_seconds')
                ->default(0);

            $table->timestamp('last_read_at')->nullable();

            $table->timestamps();

            $table->unique([
                'user_id',
                'book_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_book_progress');
    }
};