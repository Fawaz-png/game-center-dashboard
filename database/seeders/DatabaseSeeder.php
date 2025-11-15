<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create core roles
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
        ], [
            'description' => 'Administrator role with full access',
        ]);

        // Create or reuse an admin user
        $admin = User::where('email', 'admin@example.com')->first();

        if (! $admin) {
            $admin = User::factory()->create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        // Attach admin role without duplicating pivot rows
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
