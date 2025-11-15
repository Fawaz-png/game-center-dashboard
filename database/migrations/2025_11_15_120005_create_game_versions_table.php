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
        Schema::create('game_versions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('game_id')->constrained('games')->cascadeOnDelete();
            $table->string('version_name', 120);
            $table->json('metadata')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->softDeletes();

            $table->index('game_id');
            $table->index('version_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_versions');
    }
};
