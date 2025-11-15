<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(),
            'metadata' => [
                'platform' => $this->faker->randomElement(['PS5', 'PS4', 'VR', 'Arcade']),
                'age_rating' => $this->faker->randomElement(['E', 'T', 'M', 'AO']),
                'controller_count' => $this->faker->numberBetween(1, 4),
                'tags' => [$this->faker->word(), $this->faker->word()],
            ],
            'is_active' => true,
        ];
    }

    /**
     * Indicate the game is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
