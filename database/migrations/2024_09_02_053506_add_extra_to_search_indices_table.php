<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('search_indices', function (Blueprint $table) {
            $table->json('extra');
        });
    }

    public function down(): void
    {
        Schema::table('search_indices', function (Blueprint $table) {
            $table->dropColumn('extra');
        });
    }
};
