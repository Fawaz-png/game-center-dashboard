<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_game_category', function (Blueprint $table) {
            $table->foreignUlid('game_id')->constrained('games')->cascadeOnDelete();
            $table->foreignUlid('game_category_id')->references('id')->on('game_categories')->cascadeOnDelete();
            $table->timestampsTz();

            $table->primary(['game_id', 'game_category_id']); // composite primary key
            $table->index('game_id');
            $table->index('game_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_game_category');
    }
};

