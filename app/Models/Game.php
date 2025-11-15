<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'games';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'metadata',
        'is_active',
        'active_version_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metadata' => 'json',
        'is_active' => 'boolean',
    ];

    /**
     * The categories this game belongs to.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(GameCategory::class, 'game_game_category')->withTimestamps();
    }

    /**
     * The versions of this game.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(GameVersion::class);
    }

    /**
     * The active version of this game.
     */
    public function activeVersion(): BelongsTo
    {
        return $this->belongsTo(GameVersion::class, 'active_version_id');
    }

    /**
     * The stations where this game is supported.
     */
    public function stations(): BelongsToMany
    {
        return $this->belongsToMany(Station::class, 'station_supported_games')
            ->withPivot('is_default_for_station', 'metadata')
            ->withTimestamps();
    }

    /**
     * The pricing rule assignments for this game.
     */
    public function pricingRuleAssignments()
    {
        return $this->morphMany(PricingRuleAssignment::class, 'assignable');
    }

    /**
     * Get active pricing rule assignments, ordered by priority.
     */
    public function activePricingRules()
    {
        return $this->pricingRuleAssignments()
            ->where('is_active', true)
            ->orderBy('priority', 'asc')
            ->with('pricingRule');
    }
}
