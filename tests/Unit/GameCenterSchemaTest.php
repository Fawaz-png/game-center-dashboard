<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GameVersion;
use App\Models\Station;
use App\Models\StationCategory;
use App\Models\PricingRule;
use App\Models\PricingRuleAssignment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;


class GameCenterSchemaTest extends TestCase
{
    use RefreshDatabase;

    // ========== GAME TESTS ==========

    /** @test */
    public function game_can_be_created_with_metadata()
    {
        $game = Game::factory()->create([
            'metadata' => ['platform' => 'PS5', 'rating' => 'M'],
        ]);

        $this->assertNotNull($game->id);
        $this->assertEquals('PS5', $game->metadata['platform']);
        $this->assertTrue($game->is_active);
    }

    /** @test */
    public function game_can_have_many_categories()
    {
        $game = Game::factory()->create();
        $categories = GameCategory::factory()->count(3)->create();

        $game->categories()->attach($categories);

        $this->assertCount(3, $game->categories);
        $this->assertTrue($game->categories->contains($categories[0]));
    }

    /** @test */
    public function game_can_have_many_versions()
    {
        $game = Game::factory()->create();
        $versions = GameVersion::factory()->count(2)->create(['game_id' => $game->id]);

        $this->assertCount(2, $game->versions);
        $this->assertTrue($game->versions->contains($versions[0]));
    }

    /** @test */
    public function game_can_have_active_version()
    {
        $game = Game::factory()->create();
        $version = GameVersion::factory()->create(['game_id' => $game->id]);
        $game->update(['active_version_id' => $version->id]);

        $this->assertEquals($version->id, $game->activeVersion->id);
    }

    /** @test */
    public function game_can_be_supported_by_stations()
    {
        $game = Game::factory()->create();
        $stations = Station::factory()->count(2)->create();

        $game->stations()->attach($stations, ['is_default_for_station' => false]);

        $this->assertCount(2, $game->stations);
    }

    /** @test */
    public function game_can_have_pricing_rule_assignments()
    {
        $game = Game::factory()->create();
        $pricingRule = PricingRule::factory()->create();

        PricingRuleAssignment::create([
            'pricing_rule_id' => $pricingRule->id,
            'assignable_id' => $game->id,
            'assignable_type' => Game::class,
            'priority' => 1,
            'is_active' => true,
        ]);

        $this->assertCount(1, $game->pricingRuleAssignments);
        $this->assertEquals($pricingRule->id, $game->pricingRuleAssignments[0]->pricingRule->id);
    }

    /** @test */
    public function game_can_get_active_pricing_rules_ordered_by_priority()
    {
        $game = Game::factory()->create();
        $rule1 = PricingRule::factory()->create();
        $rule2 = PricingRule::factory()->create();

        PricingRuleAssignment::create([
            'pricing_rule_id' => $rule2->id,
            'assignable_id' => $game->id,
            'assignable_type' => Game::class,
            'priority' => 2,
            'is_active' => true,
        ]);

        PricingRuleAssignment::create([
            'pricing_rule_id' => $rule1->id,
            'assignable_id' => $game->id,
            'assignable_type' => Game::class,
            'priority' => 1,
            'is_active' => true,
        ]);

        $activePricing = $game->activePricingRules()->get();
        $this->assertCount(2, $activePricing);
        $this->assertEquals(1, $activePricing[0]->priority);
        $this->assertEquals(2, $activePricing[1]->priority);
    }

    /** @test */
    public function game_soft_delete_preserves_category_assignments()
    {
        $game = Game::factory()->create();
        $category = GameCategory::factory()->create();
        $game->categories()->attach($category);

        $game->delete();

        $this->assertSoftDeleted($game);
        $this->assertTrue($game->categories()->exists());
    }

    /** @test */
    public function game_force_delete_cascades_to_pivot_tables()
    {
        $game = Game::factory()->create();
        $category = GameCategory::factory()->create();
        $game->categories()->attach($category);

        $game->forceDelete();

        $this->assertModelMissing($game);
        $this->assertFalse($game->categories()->exists());
    }

    // ========== STATION TESTS ==========

    /** @test */
    public function station_can_be_created_with_status()
    {
        $station = Station::factory()->available()->create();

        $this->assertEquals('available', $station->status);
    }

    /** @test */
    public function station_can_have_valid_status_transitions()
    {
        $station = Station::factory()->available()->create();

        $validNextStatuses = $station->getValidNextStatuses();
        $this->assertContains('occupied', $validNextStatuses);
        $this->assertContains('maintenance', $validNextStatuses);
        $this->assertContains('offline', $validNextStatuses);
    }

    /** @test */
    public function station_cannot_transition_to_invalid_status()
    {
        $station = Station::factory()->available()->create();

        $this->assertFalse($station->canTransitionTo('available'));
    }

    /** @test */
    public function station_can_transition_to_occupied_from_available()
    {
        $station = Station::factory()->available()->create();

        $this->assertTrue($station->canTransitionTo('occupied'));
    }

    /** @test */
    public function station_maintenance_valid_transitions()
    {
        $station = Station::factory()->maintenance()->create();
        $validNextStatuses = $station->getValidNextStatuses();

        $this->assertContains('available', $validNextStatuses);
        $this->assertContains('offline', $validNextStatuses);
        $this->assertNotContains('occupied', $validNextStatuses);
    }

    /** @test */
    public function station_can_support_multiple_games()
    {
        $station = Station::factory()->create();
        $games = Game::factory()->count(3)->create();

        $station->games()->attach($games, ['is_default_for_station' => false]);

        $this->assertCount(3, $station->games);
    }

    /** @test */
    public function station_can_have_categories()
    {
        $station = Station::factory()->create();
        $categories = StationCategory::factory()->count(2)->create();

        $station->categories()->attach($categories);

        $this->assertCount(2, $station->categories);
    }

    /** @test */
    public function station_can_have_pricing_rule_assignments()
    {
        $station = Station::factory()->create();
        $pricingRule = PricingRule::factory()->create();

        PricingRuleAssignment::create([
            'pricing_rule_id' => $pricingRule->id,
            'assignable_id' => $station->id,
            'assignable_type' => Station::class,
            'priority' => 1,
            'is_active' => true,
        ]);

        $this->assertCount(1, $station->pricingRuleAssignments);
    }

    // ========== PRICING RULE TESTS ==========

    /** @test */
    public function pricing_rule_can_be_created()
    {
        $rule = PricingRule::factory()->timeBasedRule()->create();

        $this->assertEquals('time', $rule->type);
        $this->assertTrue($rule->is_active);
    }

    /** @test */
    public function pricing_rule_validates_calculation_json_on_save()
    {
        $this->expectException(\InvalidArgumentException::class);

        PricingRule::create([
            'name' => 'Invalid Rule',
            'type' => 'time',
            'currency' => 'NGN',
            'price_in_cents' => 1000,
            'calculation_json' => ['type' => 'invalid'], // wrong type
        ]);
    }

    /** @test */
    public function pricing_rule_allows_null_calculation_json()
    {
        $rule = PricingRule::create([
            'name' => 'Flexible Rule',
            'type' => 'flat',
            'currency' => 'NGN',
            'price_in_cents' => 5000,
            'calculation_json' => null, // allowed
        ]);

        $this->assertNull($rule->calculation_json);
    }

    /** @test */
    public function pricing_rule_can_have_many_assignments()
    {
        $rule = PricingRule::factory()->create();
        $games = Game::factory()->count(2)->create();

        foreach ($games as $game) {
            PricingRuleAssignment::create([
                'pricing_rule_id' => $rule->id,
                'assignable_id' => $game->id,
                'assignable_type' => Game::class,
                'priority' => 1,
                'is_active' => true,
            ]);
        }

        $this->assertCount(2, $rule->assignments);
    }

    /** @test */
    public function pricing_rule_assignment_cascade_deletes_on_rule_delete()
    {
        $rule = PricingRule::factory()->create();
        $game = Game::factory()->create();

        PricingRuleAssignment::create([
            'pricing_rule_id' => $rule->id,
            'assignable_id' => $game->id,
            'assignable_type' => Game::class,
            'priority' => 1,
            'is_active' => true,
        ]);

        $rule->forceDelete();

        $this->assertEquals(0, PricingRuleAssignment::count());
    }

    // ========== CASCADE DELETE TESTS ==========

    /** @test */
    public function game_hard_delete_cascades_to_category_pivot()
    {
        $game = Game::factory()->create();
        $category = GameCategory::factory()->create();
        $game->categories()->attach($category);

        $gameId = $game->id;
        $game->forceDelete();

        $this->assertEquals(0, \DB::table('game_game_category')->where('game_id', $gameId)->count());
    }

    /** @test */
    public function game_hard_delete_cascades_to_station_pivot()
    {
        $game = Game::factory()->create();
        $station = Station::factory()->create();
        $game->stations()->attach($station);

        $gameId = $game->id;
        $game->forceDelete();

        $this->assertEquals(0, \DB::table('station_supported_games')->where('game_id', $gameId)->count());
    }

    /** @test */
    public function station_hard_delete_cascades_to_game_pivot()
    {
        $station = Station::factory()->create();
        $game = Game::factory()->create();
        $station->games()->attach($game);

        $stationId = $station->id;
        $station->forceDelete();

        $this->assertEquals(0, \DB::table('station_supported_games')->where('station_id', $stationId)->count());
    }

    // ========== POLYMORPHIC RELATIONSHIP TESTS ==========

    /** @test */
    public function pricing_rule_assignment_morph_to_game()
    {
        $game = Game::factory()->create();
        $rule = PricingRule::factory()->create();

        $assignment = PricingRuleAssignment::create([
            'pricing_rule_id' => $rule->id,
            'assignable_id' => $game->id,
            'assignable_type' => Game::class,
            'priority' => 1,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Game::class, $assignment->assignable);
        $this->assertEquals($game->id, $assignment->assignable->id);
    }

    /** @test */
    public function pricing_rule_assignment_morph_to_station()
    {
        $station = Station::factory()->create();
        $rule = PricingRule::factory()->create();

        $assignment = PricingRuleAssignment::create([
            'pricing_rule_id' => $rule->id,
            'assignable_id' => $station->id,
            'assignable_type' => Station::class,
            'priority' => 1,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Station::class, $assignment->assignable);
        $this->assertEquals($station->id, $assignment->assignable->id);
    }

    /** @test */
    public function pricing_rule_assignment_scopes_work()
    {
        $rule = PricingRule::factory()->create();
        $game1 = Game::factory()->create();
        $game2 = Game::factory()->create();

        PricingRuleAssignment::create([
            'pricing_rule_id' => $rule->id,
            'assignable_id' => $game1->id,
            'assignable_type' => Game::class,
            'priority' => 2,
            'is_active' => true,
        ]);

        PricingRuleAssignment::create([
            'pricing_rule_id' => $rule->id,
            'assignable_id' => $game2->id,
            'assignable_type' => Game::class,
            'priority' => 1,
            'is_active' => false,
        ]);

        $activeAssignments = PricingRuleAssignment::active()->get();
        $this->assertCount(1, $activeAssignments);

        $byPriority = PricingRuleAssignment::active()->byPriority()->get();
        $this->assertEquals(2, $byPriority[0]->priority);
    }
}
