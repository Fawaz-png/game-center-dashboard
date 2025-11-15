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
        if (! Schema::hasTable('role_user')) {
            return;
        }

        // If an `id` column exists and is the primary key, drop it and make a composite PK
        if (Schema::hasColumn('role_user', 'id')) {
            // Drop primary key if present, then drop the id column
            DB::statement('ALTER TABLE `role_user` DROP PRIMARY KEY');
            Schema::table('role_user', function (Blueprint $table) {
                if (Schema::hasColumn('role_user', 'id')) {
                    $table->dropColumn('id');
                }
            });

            // Ensure user_id and role_id are indexed and not null (should already be)
            DB::statement('ALTER TABLE `role_user` MODIFY `user_id` CHAR(26) NOT NULL');
            DB::statement('ALTER TABLE `role_user` MODIFY `role_id` CHAR(26) NOT NULL');

            // Add composite primary key
            DB::statement('ALTER TABLE `role_user` ADD PRIMARY KEY (`user_id`, `role_id`)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('role_user')) {
            return;
        }

        // If composite primary exists, drop it and recreate id column
        // This may fail if primary key names differ; keep it minimal.
        DB::statement('ALTER TABLE `role_user` DROP PRIMARY KEY');

        Schema::table('role_user', function (Blueprint $table) {
            $table->ulid('id')->primary();
        });
    }
};
