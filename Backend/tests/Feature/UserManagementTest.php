<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Exceptions\UnauthorizedActionException;
use App\Exceptions\UserNotFoundException;
use App\Services\UserService;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $userService;
    protected $accountManager;
    protected $regularUser;

    public function setUp(): void
    {
        parent::setUp();

        // Create roles directly with specific primary keys using DB insert to avoid auto-increment issues
        DB::table('roles')->insert([
            ['role_id' => 1, 'name' => 'account_manager', 'description' => 'Account Manager', 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 4, 'name' => 'employee', 'description' => 'Employee', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create users directly
        $this->accountManager = User::create([
            'name' => 'Admin User',
            'role_id' => 1, // Use explicit ID
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'preference' => 'all_snacks'
        ]);

        $this->regularUser = User::create([
            'name' => 'Regular User',
            'role_id' => 4, // Use explicit ID
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'preference' => 'all_snacks'
        ]);

        $this->userService = new UserService(new UserRepository());
    }

    public function test_account_manager_can_access_account_manager_users()
    {
        $this->actingAs($this->accountManager);

        $user = $this->userService->getUser($this->accountManager->user_id);

        $this->assertNotNull($user);
        $this->assertEquals($this->accountManager->user_id, $user->user_id);
    }

    public function test_regular_user_cannot_access_account_manager_users()
    {
        $this->actingAs($this->regularUser);

        $this->expectException(UnauthorizedActionException::class);
        $this->expectExceptionMessage('Access denied to account manager users');

        $this->userService->getUser($this->accountManager->user_id);
    }

    public function test_account_managers_excluded_from_general_listings()
    {
        $this->actingAs($this->accountManager);

        $users = $this->userService->listUsers();

        // Should only return non-account-manager users (1 employee)
        $this->assertEquals(1, $users->count());

        // Verify no account managers in the results
        foreach ($users as $user) {
            $this->assertNotEquals('account_manager', $user->role->name);
        }

        // Verify regular user is in the results  
        $regularUserFound = $users->contains('user_id', $this->regularUser->user_id);
        $this->assertTrue($regularUserFound);
    }

    public function test_cannot_delete_account_manager_users()
    {
        $this->actingAs($this->accountManager);

        $this->expectException(UnauthorizedActionException::class);
        $this->expectExceptionMessage('Account manager users cannot be deleted');

        $this->userService->deleteUser($this->accountManager->user_id);
    }

    public function test_only_account_managers_can_create_account_manager_users()
    {
        $this->actingAs($this->regularUser);

        $this->expectException(UnauthorizedActionException::class);
        $this->expectExceptionMessage('Only account managers can create account manager users');

        $this->userService->createUser([
            'name' => 'New Admin',
            'email' => 'newadmin@test.com',
            'password' => 'password',
            'role_id' => Role::ACCOUNT_MANAGER
        ]);
    }

    public function test_user_not_found_exception()
    {
        $this->actingAs($this->accountManager);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $this->userService->getUser(99999);
    }

    public function test_account_manager_can_update_account_manager_users()
    {
        $this->actingAs($this->accountManager);

        $updatedUser = $this->userService->updateUser($this->accountManager->user_id, [
            'name' => 'Updated Admin Name'
        ]);

        $this->assertNotNull($updatedUser);
        $this->assertEquals('Updated Admin Name', $updatedUser->name);
    }

    public function test_regular_user_cannot_update_account_manager_users()
    {
        $this->actingAs($this->regularUser);

        $this->expectException(UnauthorizedActionException::class);
        $this->expectExceptionMessage('Only account managers can update account manager users');

        $this->userService->updateUser($this->accountManager->user_id, [
            'name' => 'Updated Admin Name'
        ]);
    }

    public function test_only_account_managers_can_assign_account_manager_roles()
    {
        $this->actingAs($this->regularUser);

        $this->expectException(UnauthorizedActionException::class);
        $this->expectExceptionMessage('Only account managers can assign or change account manager roles');

        $this->userService->assignRole($this->regularUser->user_id, Role::ACCOUNT_MANAGER);
    }

    public function test_account_manager_can_assign_account_manager_roles()
    {
        $this->actingAs($this->accountManager);

        $updatedUser = $this->userService->assignRole($this->regularUser->user_id, Role::ACCOUNT_MANAGER);

        $this->assertNotNull($updatedUser);
        $this->assertEquals(Role::ACCOUNT_MANAGER, $updatedUser->role_id);
    }
}
