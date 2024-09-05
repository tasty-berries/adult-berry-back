<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comic_pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comic_id')
                  ->constrained('comics')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreignId('image_id')
                  ->constrained('files')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comic_pages');
    }
};
