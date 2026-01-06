<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GameCategory;
use App\Models\GameSubcategory;
use Illuminate\Support\Str;

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
        //
        $name = fake()->unique()->streetName();
        $min = fake()->numberBetween(1, 2);

        return [

            //Foreign Keys
            'game_category_id' => function () {
                return GameCategory::factory()->create()->id;
            },

            'game_subcategory_id' => function (array $attributes) {
                // We use the ID from step 1
                return GameSubcategory::factory()->create([
                    'game_category_id' => $attributes['game_category_id']
                ])->id; // 🚨 CRITICAL: We must return the ->id, not the factory object!
            },

            //Rest of the table fields
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'image_path' => Str::slug($name) . '.jpg',
            'is_active' => fake()->boolean(),
            'min_players' => $min,
            'max_players' => fake()->numberBetween($min, 4),
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
