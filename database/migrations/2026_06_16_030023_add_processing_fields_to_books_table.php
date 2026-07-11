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
        Schema::table('books', function (Blueprint $table) {
            $table->timestamp('processing_started_at')
                ->nullable()
                ->after('status');

            $table->timestamp('processing_finished_at')
                ->nullable()
                ->after('processing_started_at');

            $table->unsignedInteger('processing_seconds')
                ->nullable()
                ->after('processing_finished_at');

            $table->text('last_error')
                ->nullable()
                ->after('processing_seconds');

            $table->string('offline_package_path')
                ->nullable()
                ->after('last_error');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            //
        });
    }
};
