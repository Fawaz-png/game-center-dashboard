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
        Schema::create('pricing_rule_assignments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('pricing_rule_id')->constrained('pricing_rules')->cascadeOnDelete();
            $table->ulid('assignable_id');
            $table->string('assignable_type');
            $table->integer('priority')->default(1)->index();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();

            $table->index('pricing_rule_id');
            $table->index(['assignable_type', 'assignable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rule_assignments');
    }
};
