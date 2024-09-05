<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comics', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->foreignId('preview_id')
                  ->nullable()
                  ->constrained('files')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();

            $table->string('link');
            $table->bigInteger('views')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comics');
    }
};
