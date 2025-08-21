<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PaymentMethod;
use App\Models\Shop;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'contact_number' => fake()->numerify('##########'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (Shop $shop) {
            // Attach random payment methods to the shop
            $paymentMethods = PaymentMethod::inRandomOrder()->take(fake()->numberBetween(2, 4))->get();
            $shop->paymentMethods()->attach($paymentMethods, [
                'is_active' => true,
                'additional_details' => $this->generateAdditionalDetails($paymentMethods),
            ]);
        });
    }

    /**
     * Generate additional details for payment methods.
     */
    private function generateAdditionalDetails($paymentMethods)
    {
        $details = [];
        foreach ($paymentMethods as $method) {
            switch ($method->name) {
                case 'upi':
                    $details[$method->id] = ['upi_id' => fake()->numerify('##########@upi')];
                    break;
                case 'bank_transfer':
                    $details[$method->id] = [
                        'account_number' => fake()->numerify('##########'),
                        'ifsc_code' => fake()->regexify('[A-Z]{4}0[A-Z0-9]{6}'),
                        'bank_name' => fake()->randomElement(['HDFC Bank', 'ICICI Bank', 'SBI', 'Axis Bank']),
                    ];
                    break;
                case 'card':
                    $details[$method->id] = ['card_types' => ['visa', 'mastercard', 'rupay']];
                    break;
                default:
                    $details[$method->id] = [];
            }
        }
        return $details;
    }
} 