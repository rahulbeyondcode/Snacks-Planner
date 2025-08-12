<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $group;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a role and user for testing
        $role = Role::factory()->create(['name' => 'account_manager']);
        $this->user = User::factory()->create(['role_id' => $role->role_id]);
        
        // Create a group for testing
        $this->group = Group::factory()->create();
    }

    public function test_index_returns_groups_for_account_manager()
    {
        // Arrange
        $groups = Group::factory()->count(3)->create();

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/groups');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_index_returns_forbidden_for_non_account_manager()
    {
        // Arrange
        $role = Role::factory()->create(['name' => 'employee']);
        $employee = User::factory()->create(['role_id' => $role->role_id]);

        // Act & Assert
        $response = $this->actingAs($employee)
            ->getJson('/api/v1/groups');

        $response->assertStatus(500)
            ->assertJson(['message' => 'Forbidden']);
    }

    public function test_show_returns_group_for_account_manager()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/groups/{$this->group->group_id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $this->group->name);
    }

    public function test_show_returns_error_when_group_not_found()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/groups/999');

        $response->assertStatus(500)
            ->assertJson(['message' => 'Group not found']);
    }

    public function test_store_creates_new_group_successfully()
    {
        // Arrange
        $employees = User::factory()->count(2)->create(['role_id' => Role::factory()->create(['name' => 'employee'])->role_id]);
        $snackManagers = User::factory()->count(2)->create(['role_id' => Role::factory()->create(['name' => 'snack_manager'])->role_id]);
        
        $groupData = [
            'name' => 'Test Group',
            'description' => 'Test Description',
            'employees' => $employees->pluck('user_id')->toArray(),
            'snack_managers' => $snackManagers->pluck('user_id')->toArray(),
            'sort_order' => 1
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/groups', $groupData);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Test Group');

        // Verify group was created in database
        $this->assertDatabaseHas('groups', [
            'name' => 'Test Group',
            'description' => 'Test Description'
        ]);
    }

    public function test_store_prevents_account_manager_from_being_employee()
    {
        // Arrange
        $groupData = [
            'name' => 'Test Group',
            'description' => 'Test Description',
            'employees' => [$this->user->user_id, 999], // user_id is the account manager
            'snack_managers' => [888, 777],
            'sort_order' => 1
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/groups', $groupData);

        $response->assertStatus(500)
            ->assertJson(['message' => 'Account manager cannot be added as an employee.']);
    }

    public function test_store_prevents_account_manager_from_being_snack_manager()
    {
        // Arrange
        $groupData = [
            'name' => 'Test Group',
            'description' => 'Test Description',
            'employees' => [888, 777],
            'snack_managers' => [$this->user->user_id, 999], // user_id is the account manager
            'sort_order' => 1
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/groups', $groupData);

        $response->assertStatus(500)
            ->assertJson(['message' => 'Account manager cannot be added as a snack manager.']);
    }

    public function test_store_validates_required_fields()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/groups', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'employees', 'snack_managers']);
    }

    public function test_store_validates_unique_name()
    {
        // Arrange
        Group::factory()->create(['name' => 'Existing Group']);

        $groupData = [
            'name' => 'Existing Group',
            'employees' => [888, 777],
            'snack_managers' => [666, 555]
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/groups', $groupData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_updates_group_successfully()
    {
        // Arrange
        $employees = User::factory()->count(2)->create(['role_id' => Role::factory()->create(['name' => 'employee'])->role_id]);
        $snackManagers = User::factory()->count(2)->create(['role_id' => Role::factory()->create(['name' => 'snack_manager'])->role_id]);
        
        $updateData = [
            'name' => 'Updated Group',
            'description' => 'Updated Description',
            'employees' => $employees->pluck('user_id')->toArray(),
            'snack_managers' => $snackManagers->pluck('user_id')->toArray()
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/groups/{$this->group->group_id}", $updateData);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Updated Group');

        // Verify group was updated in database
        $this->assertDatabaseHas('groups', [
            'group_id' => $this->group->group_id,
            'name' => 'Updated Group',
            'description' => 'Updated Description'
        ]);
    }

    public function test_update_returns_error_when_group_not_found()
    {
        // Arrange
        $updateData = [
            'name' => 'Updated Group',
            'employees' => [888, 777],
            'snack_managers' => [666, 555]
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->putJson('/api/v1/groups/999', $updateData);

        $response->assertStatus(500)
            ->assertJson(['message' => 'Group not found']);
    }

    public function test_destroy_deletes_group_successfully()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/groups/{$this->group->group_id}");

        $response->assertStatus(204);

        // Verify group was soft deleted
        $this->assertSoftDeleted('groups', ['group_id' => $this->group->group_id]);
    }

    public function test_destroy_returns_error_when_group_not_found()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/v1/groups/999');

        $response->assertStatus(500)
            ->assertJson(['message' => 'Group not found']);
    }

    public function test_members_returns_group_members()
    {
        // Arrange
        $members = User::factory()->count(3)->create();
        foreach ($members as $member) {
            GroupMember::factory()->create([
                'group_id' => $this->group->group_id,
                'user_id' => $member->user_id
            ]);
        }

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/groups/{$this->group->group_id}/members");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_add_members_adds_members_successfully()
    {
        // Arrange
        $newMembers = User::factory()->count(2)->create();
        $memberIds = $newMembers->pluck('user_id')->toArray();

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/groups/{$this->group->group_id}/members", [
                'user_ids' => $memberIds
            ]);

        $response->assertStatus(200);

        // Verify members were added
        foreach ($memberIds as $userId) {
            $this->assertDatabaseHas('group_members', [
                'group_id' => $this->group->group_id,
                'user_id' => $userId
            ]);
        }
    }

    public function test_remove_members_removes_members_successfully()
    {
        // Arrange
        $members = User::factory()->count(2)->create();
        foreach ($members as $member) {
            GroupMember::factory()->create([
                'group_id' => $this->group->group_id,
                'user_id' => $member->user_id
            ]);
        }

        $memberIds = $members->pluck('user_id')->toArray();

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/groups/{$this->group->group_id}/members", [
                'user_ids' => $memberIds
            ]);

        $response->assertStatus(200);

        // Verify members were removed
        foreach ($memberIds as $userId) {
            $this->assertDatabaseMissing('group_members', [
                'group_id' => $this->group->group_id,
                'user_id' => $userId
            ]);
        }
    }

    public function test_set_sort_order_updates_sort_orders()
    {
        // Arrange
        $groups = Group::factory()->count(2)->create();
        $sortOrders = [
            $groups[0]->group_id => 1,
            $groups[1]->group_id => 2
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/groups/sort-order', ['sort_orders' => $sortOrders]);

        $response->assertStatus(204);

        // Verify sort orders were updated
        $this->assertDatabaseHas('groups', [
            'group_id' => $groups[0]->group_id,
            'sort_order' => 1
        ]);
        $this->assertDatabaseHas('groups', [
            'group_id' => $groups[1]->group_id,
            'sort_order' => 2
        ]);
    }

    public function test_set_sort_order_returns_error_for_invalid_input()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/groups/sort-order', ['sort_orders' => 'invalid']);

        $response->assertStatus(500)
            ->assertJson(['message' => 'Invalid input format. Expected an array.']);
    }
} 