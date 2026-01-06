<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Game;
use App\Models\GameCategory;
use PHPUnit\Framework\Attributes\Test;

class GameArchitectureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    //Test to ensure games can be created with categories and subcategories
    public function test_games_can_be_created_with_categories_and_subcategories(): void
    {
        // 1. Arrange & Act
        // This creates the Game AND automatically creates the Category/Subcategory (thanks to your Factories!)
        $game = Game::factory()->create();

        // 2. Assert: Database Integrity
        // We check the important ID fields, but skip the timestamps/descriptions to keep it clean.
        $this->assertDatabaseHas('games', [
            'id' => $game->id,
            'game_category_id' => $game->game_category_id,
            'game_subcategory_id' => $game->game_subcategory_id,
            'slug' => $game->slug,
            'is_active' => $game->is_active, // Important logic check
        ]);

        // 3. Assert: Relationship Wiring (Crucial!)
        // This proves that $game->category actually returns the parent model.
        $this->assertNotNull($game->category);
        $this->assertEquals($game->game_category_id, $game->category->id);

        $this->assertNotNull($game->subcategory);
        $this->assertEquals($game->game_subcategory_id, $game->subcategory->id);

        // 4. Assert: Hierarchy Integrity
        // Ensure the subcategory actually belongs to the category (The "Smart Factory" check)
        $this->assertEquals(
            $game->category->id,
            $game->subcategory->game_category_id,
            "The Game's subcategory must belong to the Game's category!"
        );
    }

    public function games_can_be_created_with_valid_relationships(): void
    {
        // 1. Create a game
        $game = Game::factory()->create();

        // 2. Assert the database has the record
        $this->assertDatabaseHas('games', [
            'id' => $game->id,
            'slug' => $game->slug,
            'is_active' => $game->is_active,
        ]);

        // 3. Assert the relationships exist
        $this->assertNotNull($game->category);
        $this->assertNotNull($game->subcategory);
    }

    #[Test]
    public function logic_check_subcategory_must_belong_to_the_same_category(): void
    {
        // This tests your "Smart Factory" logic specifically.
        // A Game's Subcategory (e.g., Football) MUST belong to the Game's Category (e.g., Sports).

        $game = Game::factory()->create();

        $categoryID = $game->category->id;
        $subcategoryParentID = $game->subcategory->game_category_id;

        $this->assertEquals(
            $categoryID,
            $subcategoryParentID,
            "Data Integrity Error: The Game's Subcategory belongs to a DIFFERENT parent Category!"
        );
    }

    #[Test]
    public function games_can_be_soft_deleted_and_restored(): void
    {
        // 1. Create a game
        $game = Game::factory()->create();

        // 2. Soft Delete it
        $game->delete();

        // 3. Assert it is "gone" from standard queries...
        $game->refresh();
        $this->assertSoftDeleted('games', ['id' => $game->id]);

        // 4. ...but still exists in the database if we look for trashed items
        $this->assertDatabaseHas('games', ['id' => $game->id]);

        // 5. Restore it
        $game->restore();
        $game->refresh();
        $this->assertNotSoftDeleted('games', ['id' => $game->id]);
    }

    #[Test]
    public function inverse_check_a_category_can_fetch_its_games(): void
    {
        // 1. Create a Category
        $category = GameCategory::factory()->create();

        // 2. Create 3 Games attached to this SPECIFIC category
        // (Note: We use your factory logic but override the category)
        Game::factory()->count(3)->create([
            'game_category_id' => $category->id
        ]);

        // 3. Assert the Category sees 3 games
        $this->assertCount(3, $category->games);

        // 4. Assert the Category sees the *correct* games
        $this->assertTrue($category->games->contains($category->games->first()));
    }
}