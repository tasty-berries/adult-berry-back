<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comic_title', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comic_id')
                  ->constrained('comics')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreignId('title_id')
                  ->constrained('titles')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comic_title');
    }
};
