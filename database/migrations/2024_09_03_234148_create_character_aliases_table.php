<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('character_aliases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->string('name');
            $table->string('link')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_aliases');
    }
};
