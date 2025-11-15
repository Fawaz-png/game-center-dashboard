<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PricingRuleAssignment extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'pricing_rule_assignments';

    protected $fillable = [
        'pricing_rule_id',
        'assignable_id',
        'assignable_type',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The pricing rule being assigned.
     */
    public function pricingRule(): BelongsTo
    {
        return $this->belongsTo(PricingRule::class);
    }

    /**
     * The assignable model (e.g., Game, Station).
     */
    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: get only active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: get assignments ordered by priority.
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }
}
