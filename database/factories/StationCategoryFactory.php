<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StationCategory>
 */
class StationCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['PS5', 'PS4', 'VR Rig', 'Racing Pod', 'Arcade Cabinet']);

        return [
            'name' => $name,
            'slug' => str($name)->slug(),
            'description' => $this->faker->sentence(),
            'metadata' => [
                'icon' => $this->faker->word(),
                'color' => $this->faker->hexColor(),
                'max_stations_in_category' => $this->faker->numberBetween(1, 10),
            ],
        ];
    }
}
