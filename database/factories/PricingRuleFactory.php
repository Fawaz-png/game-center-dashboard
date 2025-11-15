<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PricingRule>
 */
class PricingRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['time', 'session', 'flat', 'subscription']);

        return [
            'name' => $this->faker->unique()->words(3, true),
            'type' => $type,
            'currency' => 'NGN',
            'price_in_cents' => $this->faker->numberBetween(100, 50000),
            'billing_unit' => match ($type) {
                'time' => $this->faker->randomElement(['30_minutes', '1_hour', '2_hours']),
                'session' => 'per_session',
                'flat' => null,
                'subscription' => 'monthly',
                default => null,
            },
            'calculation_json' => [
                'type' => $type,
            ],
            'is_active' => true,
            'metadata' => [
                'description' => $this->faker->sentence(),
                'applicable_days' => $this->faker->randomElement(['weekdays', 'weekends', 'daily']),
            ],
        ];
    }

    /**
     * Create a time-based pricing rule.
     */
    public function timeBasedRule(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'time',
            'billing_unit' => '1_hour',
            'calculation_json' => ['type' => 'time'],
        ]);
    }

    /**
     * Create a session-based pricing rule.
     */
    public function sessionBasedRule(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'session',
            'billing_unit' => 'per_session',
            'calculation_json' => ['type' => 'session'],
        ]);
    }

    /**
     * Create a flat pricing rule.
     */
    public function flatRule(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'flat',
            'billing_unit' => null,
            'calculation_json' => ['type' => 'flat'],
        ]);
    }

    /**
     * Indicate the rule is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
