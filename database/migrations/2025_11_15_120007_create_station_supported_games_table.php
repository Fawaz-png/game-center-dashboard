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
        Schema::create('station_supported_games', function (Blueprint $table) {
            $table->foreignUlid('station_id')->constrained('stations')->cascadeOnDelete();
            $table->foreignUlid('game_id')->constrained('games')->cascadeOnDelete();
            $table->boolean('is_default_for_station')->default(false)->index();
            $table->json('metadata')->nullable();
            $table->timestampsTz();

            $table->unique(['station_id', 'game_id']);
            $table->index('station_id');
            $table->index('game_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_supported_games');
    }
};
