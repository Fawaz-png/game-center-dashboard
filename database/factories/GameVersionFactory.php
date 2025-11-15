<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameVersion>
 */
class GameVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'version_name' => $this->faker->unique()->bothify('v??.?'),
            'metadata' => [
                'engine_version' => $this->faker->bothify('Engine v??.??'),
                'release_year' => $this->faker->year(),
                'features' => [$this->faker->word(), $this->faker->word()],
            ],
        ];
    }
}
