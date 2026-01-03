<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use League\CommonMark\Extension\DescriptionList\Node\Description;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoleAssignmentTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_roles_can_be_assigned(): void
    {
        //Arrange; Create a user and a role
        $user = User::factory()->create();
        $role = Role::create(['name' => 'tester', 'description' => 'Test role']);


        //Act; Assign role to user, with audit info
        $user->roles()->attach($role->id, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        //Assert; Check if the role is assigned with correct audit info

        $this->assertDatabaseCount('role_user', 1);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        //check if user actually has the role
        $this->assertTrue($user->roles->contains($role));
    }

    public function test_roles_can_be_soft_deleted(): void
    {
        //Arrange; Create a user and a role
        $user = User::factory()->create();
        $role = Role::create(['name' => 'tester', 'description' => 'Test role']);

        //Act; Assign role to user, with audit info
        $user->roles()->attach($role->id, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        //Assert; Check if the role is assigned
        $this->assertDatabaseCount('role_user', 1);

        //delete the role
        $user->roles()->detach($role->id);

        //Assert; Check if the role assignment is soft deleted
        $user->refresh();
        $this->assertTrue($user->roles()->where('role_id', $role->id)->doesntExist());

        //check the pivot table for soft delete
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);

        //check that deleted_at is not null
        $pivotRecord = DB::table('role_user')
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->first();
        $this->assertNotNull($pivotRecord->deleted_at, 'The deleted_at field should not be null after soft deletion.');
    }

    public function test_assigning_the_same_role_twice_does_not_create_duplicates(): void
    {
        // 1. ARRANGE
        // Create User, Create Role
        $user = User::factory()->create();
        // Attach the role ONCE.
        $role = Role::create(['name' => 'tester', 'description' => 'Test role']);
        $user->roles()->attach($role->id, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // 2. ACT
        // Attach the EXACT SAME role AGAIN (using the same syncWithoutDetaching logic).
        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]
        ]);

        // 3. ASSERT
        // Check that the database count for 'role_user' is still exactly 1.
        $this->assertDatabaseCount('role_user', 1);
    }

    public function test_cannot_assign_non_existent_role(): void
    {
        // 1. Arrange
        $user = User::factory()->create();
        $fakeRoleId = '01FZ8Z5K0X5Q6X5K0X5Q6X5K0X'; // Assuming ULID format

        // 2. Expect Exception
        // We expect the Database to scream "Foreign Key Violation!"
        $this->expectException(\Illuminate\Database\QueryException::class);

        // 3. Act
        $user->roles()->attach($fakeRoleId);
    }

    public function test_soft_deleted_role_can_be_restored(){
        //Arrange; Create a user and a role
        $user = User::factory()->create();
        $role = Role::create(['name' => 'tester', 'description' => 'Test role']);

        //Act; Assign role to user, with audit info
        $user->roles()->attach($role->id, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        //delete the role
        $user->roles()->detach($role->id);

        //Assert; Check if the role assignment is soft deleted
        $user->refresh();
        $this->assertNotNull(
            DB::table('role_user')
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->first()->deleted_at
        );

        //Restore the role assignment by setting deleted_at to null
        DB::table('role_user')
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->update(['deleted_at' => null]);
        
        //Assert; Check if the role is restored
        $user->refresh();
        $this->assertTrue($user->roles()->where('role_id', $role->id)->exists());

        //check that deleted_at is null
        $pivotRecord = DB::table('role_user')
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->first();
        
        $this->assertNull($pivotRecord->deleted_at, 'The deleted_at field should be null after restoration.');
    }
}