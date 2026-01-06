<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GameCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameSubcategory>
 */
class GameSubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();
        
        return [
            //foreign key to GameCategory
            'game_category_id' => function () {
                return GameCategory::factory()->create()->id;
            },

            //Rest of the table fields
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
        ];
    }
}
