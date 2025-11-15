<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Station extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'stations';

    protected $fillable = [
        'name',
        'location',
        'status',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metadata' => 'json',
        'status' => 'string',
    ];

    /**
     * The games supported by this station.
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'station_supported_games')
            ->withPivot('is_default_for_station', 'metadata')
            ->withTimestamps();
    }

    /**
     * The categories this station belongs to.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(StationCategory::class, 'station_station_category')->withTimestamps();
    }

    /**
     * The pricing rule assignments for this station.
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

    /**
     * Valid status transitions for a station.
     * Returns array of statuses that can be transitioned to from current status.
     */
    public function getValidNextStatuses(): array
    {
        return match ($this->status) {
            'available' => ['occupied', 'maintenance', 'offline'],
            'occupied' => ['available', 'maintenance'],
            'maintenance' => ['available', 'offline'],
            'offline' => ['maintenance', 'available'],
            default => [],
        };
    }

    /**
     * Check if station can transition to a given status.
     */
    public function canTransitionTo(string $status): bool
    {
        return in_array($status, $this->getValidNextStatuses());
    }
}
