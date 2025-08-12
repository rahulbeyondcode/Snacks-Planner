<?php

namespace Tests\Feature;

use App\Models\SnackItem;
use App\Models\SnackShopMapping;
use App\Models\Shop;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SnackItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $shop;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a role and user for testing
        $role = Role::factory()->create(['name' => 'account_manager']);
        $this->user = User::factory()->create(['role_id' => $role->role_id]);
        
        // Create a shop for testing
        $this->shop = Shop::factory()->create();
    }

    public function test_index_returns_snacks_with_shop_mappings()
    {
        // Arrange
        $snackItem = SnackItem::factory()->create([
            'name' => 'Test Snack',
            'description' => 'Test Description'
        ]);
        
        SnackShopMapping::factory()->create([
            'snack_item_id' => $snackItem->snack_item_id,
            'shop_id' => $this->shop->shop_id,
            'snack_price' => 10.50,
            'is_available' => true
        ]);

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/snack-items');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJson(['message' => 'Snacks retrieved successfully'])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.snack_name', 'Test Snack - ' . $this->shop->name);
    }

    public function test_index_returns_empty_when_no_snacks()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/snack-items');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(0, 'data');
    }

    public function test_show_returns_snack_when_found()
    {
        // Arrange
        $snackItem = SnackItem::factory()->create([
            'name' => 'Test Snack',
            'description' => 'Test Description'
        ]);

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/snack-items/{$snackItem->snack_item_id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Test Snack')
            ->assertJsonPath('data.description', 'Test Description');
    }

    public function test_show_returns_error_when_snack_not_found()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/snack-items/999');

        $response->assertStatus(500)
            ->assertJson(['message' => 'Snack Item not found']);
    }

    public function test_store_creates_new_snack_with_mapping()
    {
        // Arrange
        $snackData = [
            'name' => 'New Snack',
            'description' => 'New Description',
            'shop_id' => $this->shop->shop_id,
            'snack_price' => 15.99,
            'is_available' => true
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/snack-items', $snackData);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Snack')
            ->assertJsonPath('data.description', 'New Description');

        // Verify snack item was created in database
        $this->assertDatabaseHas('snack_items', [
            'name' => 'New Snack',
            'description' => 'New Description'
        ]);

        // Verify shop mapping was created
        $this->assertDatabaseHas('snack_shop_mapping', [
            'shop_id' => $this->shop->shop_id,
            'snack_price' => 15.99,
            'is_available' => true
        ]);
    }

    public function test_store_creates_snack_with_default_availability()
    {
        // Arrange
        $snackData = [
            'name' => 'New Snack',
            'description' => 'New Description',
            'shop_id' => $this->shop->shop_id,
            'snack_price' => 15.99
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/snack-items', $snackData);

        $response->assertStatus(201);

        // Verify default availability was set
        $this->assertDatabaseHas('snack_shop_mapping', [
            'shop_id' => $this->shop->shop_id,
            'is_available' => true
        ]);
    }

    public function test_store_validates_required_fields()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/snack-items', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'shop_id', 'snack_price']);
    }

    public function test_update_updates_existing_snack()
    {
        // Arrange
        $snackItem = SnackItem::factory()->create([
            'name' => 'Old Name',
            'description' => 'Old Description'
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated Description'
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/snack-items/{$snackItem->snack_item_id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.description', 'Updated Description');

        // Verify snack item was updated in database
        $this->assertDatabaseHas('snack_items', [
            'snack_item_id' => $snackItem->snack_item_id,
            'name' => 'Updated Name',
            'description' => 'Updated Description'
        ]);
    }

    public function test_update_creates_new_shop_mapping()
    {
        // Arrange
        $snackItem = SnackItem::factory()->create();
        $newShop = Shop::factory()->create();
        
        $updateData = [
            'shop_id' => $newShop->shop_id,
            'snack_price' => 25.99,
            'is_available' => false
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/snack-items/{$snackItem->snack_item_id}", $updateData);

        $response->assertStatus(200);

        // Verify new shop mapping was created
        $this->assertDatabaseHas('snack_shop_mapping', [
            'snack_item_id' => $snackItem->snack_item_id,
            'shop_id' => $newShop->shop_id,
            'snack_price' => 25.99,
            'is_available' => false
        ]);
    }

    public function test_update_updates_existing_shop_mapping()
    {
        // Arrange
        $snackItem = SnackItem::factory()->create();
        
        // Create existing mapping
        SnackShopMapping::factory()->create([
            'snack_item_id' => $snackItem->snack_item_id,
            'shop_id' => $this->shop->shop_id,
            'snack_price' => 10.00,
            'is_available' => true
        ]);

        $updateData = [
            'shop_id' => $this->shop->shop_id,
            'snack_price' => 30.00,
            'is_available' => false
        ];

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/snack-items/{$snackItem->snack_item_id}", $updateData);

        $response->assertStatus(200);

        // Verify existing mapping was updated
        $this->assertDatabaseHas('snack_shop_mapping', [
            'snack_item_id' => $snackItem->snack_item_id,
            'shop_id' => $this->shop->shop_id,
            'snack_price' => 30.00,
            'is_available' => false
        ]);
    }

    public function test_update_returns_error_when_snack_not_found()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->putJson('/api/v1/snack-items/999', [
                'name' => 'Updated Name'
            ]);

        $response->assertStatus(500)
            ->assertJson(['message' => 'Snack Item not found']);
    }

    public function test_destroy_deletes_snack_and_mappings()
    {
        // Arrange
        $snackItem = SnackItem::factory()->create();
        
        SnackShopMapping::factory()->create([
            'snack_item_id' => $snackItem->snack_item_id,
            'shop_id' => $this->shop->shop_id
        ]);

        // Act & Assert
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/snack-items/{$snackItem->snack_item_id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify snack item was deleted
        $this->assertDatabaseMissing('snack_items', ['snack_item_id' => $snackItem->snack_item_id]);
        
        // Verify shop mappings were deleted
        $this->assertDatabaseMissing('snack_shop_mapping', ['snack_item_id' => $snackItem->snack_item_id]);
    }

    public function test_destroy_returns_error_when_snack_not_found()
    {
        // Act & Assert
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/v1/snack-items/999');

        $response->assertStatus(500)
            ->assertJson(['message' => 'Snack Item not found']);
    }
} 