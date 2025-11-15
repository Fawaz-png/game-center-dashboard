<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameCategory extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'game_categories';

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
     * The games in this category.
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_game_category')->withTimestamps();
    }
}
