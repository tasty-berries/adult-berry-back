<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->foreignId('preview_id')
                  ->nullable()
                  ->constrained('files')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->string('link');
            $table->bigInteger('views')->default(0);

            $table->foreignId('author_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreignId('video_id')
                  ->nullable()
                  ->constrained('files')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
