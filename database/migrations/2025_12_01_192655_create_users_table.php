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
        Schema::create('users', function (Blueprint $table) {
            // Primary Key
            $table->ulid('id')->primary();

            // User Info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique()->index();      // indexed for faster lookup
            $table->string('username')->unique()->index();   // indexed for login
            $table->string('phone_number')->index();         // optional search/filter
            $table->boolean('is_active')->default(true);

            // Auth
            $table->string('password');
            $table->rememberToken();
            $table->timestampTz('email_verified_at')->nullable();

            // Audit trail
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('last_login_at')->nullable();

            // Timestamps & Soft Deletes
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
