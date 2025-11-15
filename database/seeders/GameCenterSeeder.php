<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GameVersion;
use App\Models\Station;
use App\Models\StationCategory;
use App\Models\PricingRule;
use App\Models\PricingRuleAssignment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create game categories
        $arcadeCategory = GameCategory::firstOrCreate(
            ['slug' => 'arcade'],
            [
                'name' => 'Arcade',
                'description' => 'Classic arcade games',
                'metadata' => ['icon' => 'joystick', 'color' => '#FF6B6B'],
            ]
        );

        $consoleCategory = GameCategory::firstOrCreate(
            ['slug' => 'console'],
            [
                'name' => 'Console',
                'description' => 'Console games (PS5, PS4)',
                'metadata' => ['icon' => 'controller', 'color' => '#4ECDC4'],
            ]
        );

        $vrCategory = GameCategory::firstOrCreate(
            ['slug' => 'vr'],
            [
                'name' => 'VR',
                'description' => 'Virtual Reality experiences',
                'metadata' => ['icon' => 'vr-headset', 'color' => '#45B7D1'],
            ]
        );

        $racingCategory = GameCategory::firstOrCreate(
            ['slug' => 'racing'],
            [
                'name' => 'Racing',
                'description' => 'Racing simulations and arcade racers',
                'metadata' => ['icon' => 'racing-car', 'color' => '#F7B731'],
            ]
        );

        // Create games with categories and versions
        $elden_ring = Game::firstOrCreate(
            ['slug' => 'elden-ring'],
            [
                'name' => 'Elden Ring',
                'description' => 'A collaborative effort between FromSoftware and George R. R. Martin',
                'metadata' => ['platform' => 'PS5', 'age_rating' => 'M', 'controller_count' => 1],
                'is_active' => true,
            ]
        );

        if ($elden_ring->wasRecentlyCreated) {
            $version = GameVersion::factory()->create(['game_id' => $elden_ring->id]);
            $elden_ring->update(['active_version_id' => $version->id]);
            $elden_ring->categories()->sync([$consoleCategory->id]);
        }

        $vr_beat_saber = Game::firstOrCreate(
            ['slug' => 'beat-saber'],
            [
                'name' => 'Beat Saber',
                'description' => 'Rhythm VR game',
                'metadata' => ['platform' => 'VR', 'age_rating' => 'E', 'controller_count' => 2],
                'is_active' => true,
            ]
        );

        if ($vr_beat_saber->wasRecentlyCreated) {
            $version = GameVersion::factory()->create(['game_id' => $vr_beat_saber->id]);
            $vr_beat_saber->update(['active_version_id' => $version->id]);
            $vr_beat_saber->categories()->sync([$vrCategory->id]);
        }

        $pac_man = Game::firstOrCreate(
            ['slug' => 'pac-man'],
            [
                'name' => 'Pac-Man',
                'description' => 'Classic arcade game',
                'metadata' => ['platform' => 'Arcade', 'age_rating' => 'E', 'controller_count' => 1],
                'is_active' => true,
            ]
        );

        if ($pac_man->wasRecentlyCreated) {
            $pac_man->categories()->sync([$arcadeCategory->id]);
        }

        $gran_turismo = Game::firstOrCreate(
            ['slug' => 'gran-turismo-7'],
            [
                'name' => 'Gran Turismo 7',
                'description' => 'Racing simulator for PS5',
                'metadata' => ['platform' => 'PS5', 'age_rating' => 'T', 'controller_count' => 1],
                'is_active' => true,
            ]
        );

        if ($gran_turismo->wasRecentlyCreated) {
            $version = GameVersion::factory()->create(['game_id' => $gran_turismo->id]);
            $gran_turismo->update(['active_version_id' => $version->id]);
            $gran_turismo->categories()->sync([$racingCategory->id, $consoleCategory->id]);
        }

        // Create station categories
        $ps5Category = StationCategory::firstOrCreate(
            ['slug' => 'ps5'],
            ['name' => 'PS5', 'description' => 'PlayStation 5 stations', 'metadata' => ['color' => '#003087']]
        );

        $vrCategory2 = StationCategory::firstOrCreate(
            ['slug' => 'vr-rig'],
            ['name' => 'VR Rig', 'description' => 'VR Experience stations', 'metadata' => ['color' => '#45B7D1']]
        );

        $racingPodCategory = StationCategory::firstOrCreate(
            ['slug' => 'racing-pod'],
            ['name' => 'Racing Pod', 'description' => 'Racing simulation pods', 'metadata' => ['color' => '#F7B731']]
        );

        // Create stations
        $ps5_station_1 = Station::firstOrCreate(
            ['name' => 'PS5 Station 1'],
            [
                'location' => 'Main Hall',
                'status' => 'available',
                'metadata' => ['hardware_model' => 'PS5', 'firmware_version' => 'v1.0'],
            ]
        );

        if ($ps5_station_1->wasRecentlyCreated) {
            $ps5_station_1->categories()->attach($ps5Category);
            $ps5_station_1->games()->attach([$elden_ring->id, $gran_turismo->id], ['is_default_for_station' => false]);
        }

        $vr_rig_1 = Station::firstOrCreate(
            ['name' => 'VR Rig 1'],
            [
                'location' => 'VR Zone',
                'status' => 'available',
                'metadata' => ['hardware_model' => 'Meta Quest 3', 'firmware_version' => 'v2.5'],
            ]
        );

        if ($vr_rig_1->wasRecentlyCreated) {
            $vr_rig_1->categories()->attach($vrCategory2);
            $vr_rig_1->games()->attach($vr_beat_saber->id, ['is_default_for_station' => true]);
        }

        $racing_pod_1 = Station::firstOrCreate(
            ['name' => 'Racing Pod 1'],
            [
                'location' => 'Racing Section',
                'status' => 'available',
                'metadata' => ['hardware_model' => 'Fanatec', 'firmware_version' => 'v1.2'],
            ]
        );

        if ($racing_pod_1->wasRecentlyCreated) {
            $racing_pod_1->categories()->attach($racingPodCategory);
            $racing_pod_1->games()->attach($gran_turismo->id, ['is_default_for_station' => true]);
        }

        // Create pricing rules
        $hourlyRate = PricingRule::firstOrCreate(
            ['name' => 'Hourly Rate'],
            [
                'type' => 'time',
                'currency' => 'NGN',
                'price_in_cents' => 500000, // 5000 NGN per hour
                'billing_unit' => '1_hour',
                'calculation_json' => ['type' => 'time'],
                'is_active' => true,
                'metadata' => ['description' => 'NGN 5,000 per hour'],
            ]
        );

        $sessionRate = PricingRule::firstOrCreate(
            ['name' => 'Per Session'],
            [
                'type' => 'session',
                'currency' => 'NGN',
                'price_in_cents' => 300000, // 3000 NGN per session
                'billing_unit' => 'per_session',
                'calculation_json' => ['type' => 'session'],
                'is_active' => true,
                'metadata' => ['description' => 'NGN 3,000 per session'],
            ]
        );

        $unlimitedDaily = PricingRule::firstOrCreate(
            ['name' => 'Unlimited Daily'],
            [
                'type' => 'subscription',
                'currency' => 'NGN',
                'price_in_cents' => 1500000, // 15,000 NGN per day
                'billing_unit' => 'daily',
                'calculation_json' => ['type' => 'subscription'],
                'is_active' => true,
                'metadata' => ['description' => 'NGN 15,000 unlimited access per day'],
            ]
        );

        // Assign pricing rules to games
        PricingRuleAssignment::firstOrCreate(
            [
                'pricing_rule_id' => $hourlyRate->id,
                'assignable_id' => $elden_ring->id,
                'assignable_type' => Game::class,
            ],
            ['priority' => 1, 'is_active' => true]
        );

        PricingRuleAssignment::firstOrCreate(
            [
                'pricing_rule_id' => $sessionRate->id,
                'assignable_id' => $vr_beat_saber->id,
                'assignable_type' => Game::class,
            ],
            ['priority' => 1, 'is_active' => true]
        );

        // Assign pricing rules to stations
        PricingRuleAssignment::firstOrCreate(
            [
                'pricing_rule_id' => $hourlyRate->id,
                'assignable_id' => $ps5_station_1->id,
                'assignable_type' => Station::class,
            ],
            ['priority' => 1, 'is_active' => true]
        );

        PricingRuleAssignment::firstOrCreate(
            [
                'pricing_rule_id' => $sessionRate->id,
                'assignable_id' => $vr_rig_1->id,
                'assignable_type' => Station::class,
            ],
            ['priority' => 1, 'is_active' => true]
        );
    }
}
