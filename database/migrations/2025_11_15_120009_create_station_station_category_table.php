<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_station_category', function (Blueprint $table) {
            $table->foreignUlid('station_id')->constrained('stations')->cascadeOnDelete();
            $table->foreignUlid('station_category_id')->references('id')->on('station_categories')->cascadeOnDelete();
            $table->timestampsTz();

            $table->primary(['station_id', 'station_category_id']); // composite primary key
            $table->index('station_id');
            $table->index('station_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_station_category');
    }
};
