<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Station>
 */
class StationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Station ' . $this->faker->unique()->numberBetween(1, 100),
            'location' => $this->faker->randomElement(['VR Zone', 'Arcade Alley', 'PS5 Arena', 'Racing Section', 'Main Hall']),
            'status' => $this->faker->randomElement(['available', 'occupied', 'maintenance', 'offline']),
            'metadata' => [
                'hardware_model' => $this->faker->word(),
                'firmware_version' => $this->faker->bothify('v?.?'),
                'specs' => [
                    'ram' => $this->faker->randomElement(['8GB', '16GB', '32GB']),
                    'storage' => $this->faker->randomElement(['256GB', '512GB', '1TB']),
                ],
            ],
        ];
    }

    /**
     * Indicate the station is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }

    /**
     * Indicate the station is occupied.
     */
    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'occupied',
        ]);
    }

    /**
     * Indicate the station is in maintenance.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maintenance',
        ]);
    }
}
