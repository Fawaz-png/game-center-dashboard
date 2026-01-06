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
        Schema::create('games', function (Blueprint $table) {
            // Primary Key
            $table->ulid('id')->primary();

            //Foreign keys
            $table->foreignUlid('game_category_id')->constrained('game_categories')->cascadeOnDelete();
            $table->foreignUlid('game_subcategory_id')->nullable()->constrained('game_subcategories')->nullOnDelete();

            // Game Info
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true)->index();

            //Player logic
            $table->unsignedTinyInteger('min_players')->default(1);
            $table->unsignedTinyInteger('max_players')->default(1);


            //Soft deletes and timestamps
            $table->softDeletesTz();
            $table->timestampsTz();

            // Audit trail
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
