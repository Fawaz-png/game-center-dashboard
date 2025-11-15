<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StationCategory extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'station_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    /**
     * The stations in this category.
     */
    public function stations(): BelongsToMany
    {
        return $this->belongsToMany(Station::class, 'station_station_category')->withTimestamps();
    }
}
