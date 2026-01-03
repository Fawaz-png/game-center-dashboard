<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrator with full access',
            ],
            [
                'name' => 'staff',
                'description' => 'Staff member with limited access',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name'=> $role['name']],
                $role
            );
        }
    }
}