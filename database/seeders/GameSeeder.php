<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GameSubcategory;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // 1. Create a "Sports" Category
        $sports = GameCategory::factory()->create([
            'name' => 'Sports',
            'slug' => 'sports',
            'description' => 'Competitive physical games and simulations.'
        ]);

        // 2. Create "Football" Subcategory
        $football = GameSubcategory::factory()->create([
            'game_category_id' => $sports->id,
            'name' => 'Football',
            'slug' => 'football'
        ]);

        // 3. Add FIFA to Sports -> Football
        Game::factory()->create([
            'game_category_id' => $sports->id,
            'game_subcategory_id' => $football->id,
            'name' => 'FIFA 24',
            'slug' => 'fifa-24',
            'min_players' => 1,
            'max_players' => 4,
        ]);

        // 4. Create an "Action" Category
        $action = GameCategory::factory()->create([
            'name' => 'Action',
            'slug' => 'action'
        ]);

        // 5. Create "Fighting" Subcategory
        $fighting = GameSubcategory::factory()->create([
            'game_category_id' => $action->id,
            'name' => 'Fighting',
            'slug' => 'fighting'
        ]);

        // 6. Add Tekken & MK to Action -> Fighting
        Game::factory()->create([
            'game_category_id' => $action->id,
            'game_subcategory_id' => $fighting->id,
            'name' => 'Tekken 8',
            'slug' => 'tekken-8',
            'min_players' => 1,
            'max_players' => 2,
        ]);

        Game::factory()->create([
            'game_category_id' => $action->id,
            'game_subcategory_id' => $fighting->id,
            'name' => 'Mortal Kombat 1',
            'slug' => 'mortal-kombat-1',
            'min_players' => 1,
            'max_players' => 2,
        ]);

        // 7. Generate random filler data using your smart Factory logic
        Game::factory()->count(5)->create();
    }
}
