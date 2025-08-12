<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a role and user for testing
        $role = Role::factory()->create(['name' => 'account_manager']);
        $this->user = User::factory()->create(['role_id' => $role->role_id]);
    }

    public function test_index_returns_shop_collection()
    {
        // Arrange
        $shops = Shop::factory()->count(3)->create([
            'name' => 'Test Shop',
            'address' => 'Test Address',
            'contact_number' => '1234567890'
        ]);

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/shops');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.name', 'Test Shop');
    }

    public function test_show_returns_shop_when_found()
    {
        // Arrange
        $shop = Shop::factory()->create([
            'name' => 'Test Shop',
            'address' => 'Test Address',
            'contact_number' => '1234567890'
        ]);

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/shops/{$shop->shop_id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Test Shop')
            ->assertJsonPath('data.address', 'Test Address');
    }

    public function test_show_returns_error_when_shop_not_found()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/shops/999');

        $response->assertStatus(500)
            ->assertJson(['message' => 'Shop not found']);
    }

    public function test_store_creates_new_shop()
    {
        // Arrange
        $shopData = [
            'name' => 'New Shop',
            'address' => 'New Address',
            'contact_number' => '9876543210'
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/shops', $shopData);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Shop')
            ->assertJsonPath('data.address', 'New Address');

        // Verify shop was created in database
        $this->assertDatabaseHas('shops', $shopData);
    }

    public function test_store_validates_required_fields()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/shops', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'address']);
    }

    public function test_store_validates_unique_name()
    {
        // Arrange
        Shop::factory()->create(['name' => 'Existing Shop']);

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/shops', [
                'name' => 'Existing Shop',
                'address' => 'New Address'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_updates_existing_shop()
    {
        // Arrange
        $shop = Shop::factory()->create([
            'name' => 'Old Name',
            'address' => 'Old Address'
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'address' => 'Updated Address'
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/shops/{$shop->shop_id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.address', 'Updated Address');

        // Verify shop was updated in database
        $this->assertDatabaseHas('shops', [
            'shop_id' => $shop->shop_id,
            'name' => 'Updated Name',
            'address' => 'Updated Address'
        ]);
    }

    public function test_update_returns_error_when_shop_not_found()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->putJson('/api/v1/shops/999', [
                'name' => 'Updated Name',
                'address' => 'Updated Address'
            ]);

        $response->assertStatus(500)
            ->assertJson(['message' => 'Shop not found']);
    }

    public function test_destroy_deletes_shop()
    {
        // Arrange
        $shop = Shop::factory()->create();

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/shops/{$shop->shop_id}");

        $response->assertStatus(204);

        // Verify shop was soft deleted
        $this->assertSoftDeleted('shops', ['shop_id' => $shop->shop_id]);
    }

    public function test_destroy_returns_error_when_shop_not_found()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/v1/shops/999');

        $response->assertStatus(500)
            ->assertJson(['message' => 'messages.not_found']);
    }
} 