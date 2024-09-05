<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('character_comic', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                  ->constrained('characters')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreignId('comic_id')
                  ->constrained('comics')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_comic');
    }
};
