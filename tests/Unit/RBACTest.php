<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RBACTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a role.
     */
    public function test_create_role(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'Administrator role',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'admin',
            'description' => 'Administrator role',
        ]);

        $this->assertNotNull($role->id);
    }

    /**
     * Test role enum constraint (admin/staff).
     */
    public function test_role_enum_values(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $staff = Role::create(['name' => 'staff']);

        $this->assertEquals('admin', $admin->name);
        $this->assertEquals('staff', $staff->name);
    }

    /**
     * Test role name is unique.
     */
    public function test_role_name_is_unique(): void
    {
        Role::create(['name' => 'admin']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Role::create(['name' => 'admin']);
    }

    /**
     * Test attaching a role to a user.
     */
    public function test_attach_role_to_user(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);

        $user->roles()->attach($adminRole->id);

        $this->assertTrue($user->roles->contains($adminRole));
    }

    /**
     * Test user has many roles (many-to-many).
     */
    public function test_user_has_many_roles(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $staffRole = Role::create(['name' => 'staff']);

        $user->roles()->attach([$adminRole->id, $staffRole->id]);

        $this->assertCount(2, $user->roles);
        $this->assertTrue($user->roles->contains($adminRole));
        $this->assertTrue($user->roles->contains($staffRole));
    }

    /**
     * Test detaching a role from a user.
     */
    public function test_detach_role_from_user(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);

        $user->roles()->attach($adminRole->id);
        $this->assertTrue($user->roles->contains($adminRole));

        $user->roles()->detach($adminRole->id);
        $user->refresh();

        $this->assertFalse($user->roles->contains($adminRole));
    }

    /**
     * Test role has many users (many-to-many reverse).
     */
    public function test_role_has_many_users(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1->roles()->attach($adminRole->id);
        $user2->roles()->attach($adminRole->id);

        $this->assertCount(2, $adminRole->users);
    }

    /**
     * Test user soft delete with role preservation.
     */
    public function test_user_soft_delete_preserves_role_audit(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $user->roles()->attach($adminRole->id);

        $userId = $user->id;
        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $userId]);
        $this->assertDatabaseHas('role_user', ['user_id' => $userId]);
    }

    /**
     * Test user created_by and updated_by audit fields.
     */
    public function test_user_audit_fields(): void
    {
        $admin = User::factory()->create();
        $newUser = User::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->assertEquals($admin->id, $newUser->created_by);
        $this->assertEquals($admin->id, $newUser->updated_by);
    }

    /**
     * Test user is_active flag.
     */
    public function test_user_is_active_flag(): void
    {
        $activeUser = User::factory()->create(['is_active' => true]);
        $inactiveUser = User::factory()->create(['is_active' => false]);

        $this->assertTrue($activeUser->is_active);
        $this->assertFalse($inactiveUser->is_active);
    }

    /**
     * Test user last_login_at timestamp.
     */
    public function test_user_last_login_timestamp(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->last_login_at);

        $now = now();
        $user->update(['last_login_at' => $now]);

        // Refresh the model from the database and ensure the cast produced a Carbon instance
        $user->refresh();
        $this->assertInstanceOf(Carbon::class, $user->last_login_at);
        /** @var \Illuminate\Support\Carbon $lastLogin */
        $lastLogin = $user->last_login_at;
        $this->assertEquals($now->format('Y-m-d H:i'), $lastLogin->format('Y-m-d H:i'));
    }

    /**
     * Test pivot table cascade on delete.
     */
    public function test_role_cascade_delete_removes_pivot(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $user->roles()->attach($adminRole->id);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $adminRole->id,
        ]);

        $adminRole->delete();

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $adminRole->id,
        ]);
    }
}
