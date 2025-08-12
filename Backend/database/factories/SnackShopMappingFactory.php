<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SnackShopMapping>
 */
class SnackShopMappingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'snack_item_id' => 1,
            'shop_id' => 1,
            'snack_price' => fake()->randomFloat(2, 1, 50),
            'is_available' => fake()->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
