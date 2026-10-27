<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image', 2048)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('series_id')->constrained('series')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail_url', 2048)->nullable();
            $table->text('stream_url');
            $table->boolean('is_active')->default(true);
            $table->index(['series_id', 'is_active']);
            $table->timestamps();
        });
        Schema::create('integration_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->text('api_token')->nullable();
            $table->timestamps();
        });
        DB::table('integration_settings')->insert(['id' => 1, 'created_at' => now(), 'updated_at' => now()]);
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_settings');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('series');
    }
};
