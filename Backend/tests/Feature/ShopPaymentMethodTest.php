<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $shop;
    protected $paymentMethods;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a role and user for testing
        $role = Role::factory()->create(['name' => 'account_manager']);
        $this->user = User::factory()->create(['role_id' => $role->role_id]);
        
        // Create a shop
        $this->shop = Shop::factory()->create();
        
        // Create payment methods
        $this->paymentMethods = PaymentMethod::factory()->count(3)->create();
    }

    public function test_can_get_shop_payment_methods()
    {
        // Attach payment methods to shop
        $this->shop->paymentMethods()->attach($this->paymentMethods->pluck('payment_method_id')->toArray());

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/shops/{$this->shop->shop_id}/payment-methods");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_add_payment_methods_to_shop()
    {
        $paymentMethodIds = $this->paymentMethods->pluck('payment_method_id')->toArray();
        
        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/shops/{$this->shop->shop_id}/payment-methods", [
                'payment_method_ids' => $paymentMethodIds,
                'additional_details' => [
                    $this->paymentMethods[0]->payment_method_id => ['upi_id' => 'test@upi'],
                    $this->paymentMethods[1]->payment_method_id => ['account_number' => '1234567890'],
                ]
            ]);

        $response->assertStatus(201);
        
        // Verify payment methods were attached
        $this->assertCount(3, $this->shop->fresh()->paymentMethods);
    }

    public function test_can_update_payment_method_details()
    {
        // Attach a payment method first
        $this->shop->paymentMethods()->attach($this->paymentMethods[0]->payment_method_id);

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/shops/{$this->shop->shop_id}/payment-methods/{$this->paymentMethods[0]->payment_method_id}", [
                'additional_details' => ['upi_id' => 'updated@upi'],
                'is_active' => false
            ]);

        $response->assertStatus(200);
        
        // Verify the update
        $shopPaymentMethod = $this->shop->fresh()->paymentMethods()->first();
        $this->assertEquals(['upi_id' => 'updated@upi'], $shopPaymentMethod->pivot->additional_details);
        $this->assertFalse($shopPaymentMethod->pivot->is_active);
    }

    public function test_can_remove_payment_method_from_shop()
    {
        // Attach a payment method first
        $this->shop->paymentMethods()->attach($this->paymentMethods[0]->payment_method_id);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/shops/{$this->shop->shop_id}/payment-methods/{$this->paymentMethods[0]->payment_method_id}");

        $response->assertStatus(200);
        
        // Verify payment method was removed
        $this->assertCount(0, $this->shop->fresh()->paymentMethods);
    }

    public function test_can_toggle_payment_method_status()
    {
        // Attach a payment method first
        $this->shop->paymentMethods()->attach($this->paymentMethods[0]->payment_method_id, ['is_active' => true]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/v1/shops/{$this->shop->shop_id}/payment-methods/{$this->paymentMethods[0]->payment_method_id}/toggle");

        $response->assertStatus(200)
            ->assertJson(['is_active' => false]);
        
        // Verify the status was toggled
        $shopPaymentMethod = $this->shop->fresh()->paymentMethods()->first();
        $this->assertFalse($shopPaymentMethod->pivot->is_active);
    }

    public function test_can_get_available_payment_methods()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/payment-methods/available');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_shop_can_have_multiple_payment_methods()
    {
        $paymentMethodIds = $this->paymentMethods->pluck('payment_method_id')->toArray();
        
        $this->shop->paymentMethods()->attach($paymentMethodIds);
        
        $this->assertCount(3, $this->shop->fresh()->paymentMethods);
    }

    public function test_shop_can_have_active_and_inactive_payment_methods()
    {
        $this->shop->paymentMethods()->attach([
            $this->paymentMethods[0]->payment_method_id => ['is_active' => true],
            $this->paymentMethods[1]->payment_method_id => ['is_active' => false],
        ]);
        
        $this->assertCount(2, $this->shop->paymentMethods);
        $this->assertCount(1, $this->shop->activePaymentMethods);
    }
} 