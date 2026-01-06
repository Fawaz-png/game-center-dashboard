<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_subcategories', function (Blueprint $table) {
            //Primary Key
            $table->ulid('id')->primary();

            //foreign key
            $table->foreignUlid('game_category_id')->constrained('game_categories')->cascadeOnDelete();

            // Sub Category Info
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

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
        Schema::dropIfExists('game_subcategories');
    }
};
