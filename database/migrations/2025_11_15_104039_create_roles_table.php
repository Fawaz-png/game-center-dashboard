<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
 

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('roles')) {
            return;
        }

        Schema::create('roles', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->enum('name', ['admin', 'staff'])->unique();
            $table->text('description')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop any lingering FK constraints that reference roles (e.g., users.role_id)
        if (Schema::hasTable('users')) {
            DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `users_role_id_foreign`');
        }

        Schema::dropIfExists('roles');
    }
};
