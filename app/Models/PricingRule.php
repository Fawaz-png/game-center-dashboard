<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PricingRule extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'pricing_rules';

    protected $fillable = [
        'name',
        'type',
        'currency',
        'price_in_cents',
        'billing_unit',
        'calculation_json',
        'is_active',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'calculation_json' => 'json',
        'metadata' => 'json',
        'is_active' => 'boolean',
        'type' => 'string',
    ];

    /**
     * The pricing rule assignments for this rule.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(PricingRuleAssignment::class);
    }

    /**
     * Validate the calculation_json structure based on the pricing rule type.
     * This method can be called before saving to ensure data integrity.
     */
    public function validateCalculationJson(): bool
    {
        if ($this->calculation_json === null) {
            return true;
        }

        // calculation_json is already decoded by the json cast
        $json = is_array($this->calculation_json) ? $this->calculation_json : json_decode($this->calculation_json, true);

        if (!is_array($json)) {
            return false;
        }

        return match ($this->type) {
            'time' => isset($json['type']) && $json['type'] === 'time',
            'session' => isset($json['type']) && $json['type'] === 'session',
            'flat' => isset($json['type']) && $json['type'] === 'flat',
            'subscription' => isset($json['type']) && $json['type'] === 'subscription',
            default => false,
        };
    }

    /**
     * Boot the model to validate calculation_json on save.
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->calculation_json !== null && !$model->validateCalculationJson()) {
                throw new \InvalidArgumentException(
                    "Invalid calculation_json structure for type: {$model->type}"
                );
            }
        });
    }
}
