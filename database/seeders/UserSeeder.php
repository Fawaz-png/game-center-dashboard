<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;



class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'role_slug' => 'admin',
                'email' => 'admin@gamecenter.test',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'username' => 'admin',
                'phone_number' => '0000000000',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],

            [
                'role_slug' => 'staff',
                'email' => 'staff@gamecenter.test',
                'first_name' => 'Game',
                'last_name' => 'Staff',
                'username' => 'staff',
                'phone_number' => '0000000001',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]

        ];

        foreach ($users as $userData) {
            $roleSlug = $userData['role_slug'];
            unset($userData['role_slug']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Attach role to user
            if ($roleSlug) {
                $role = Role::where('name', $roleSlug)->first();
                if ($role) {
                    $user->roles()->syncWithoutDetaching([
                        // Attach role with audit info
                         $role->id => [
                            'created_by' => $user->id, // Self-referential for seeder
                            'updated_by' => $user->id // Self-referential for seeder
                        ]

                    ]);
                }
            }
        }
    }
}
