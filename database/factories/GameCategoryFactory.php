<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameCategory>
 */
class GameCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'name' => $name,
            'slug' => str($name)->slug(),
            'description' => $this->faker->sentence(),
            'metadata' => [
                'icon' => $this->faker->word(),
                'color' => $this->faker->hexColor(),
                'display_order' => $this->faker->numberBetween(1, 10),
            ],
        ];
    }
}
