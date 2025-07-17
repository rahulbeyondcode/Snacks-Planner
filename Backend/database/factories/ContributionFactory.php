<?php

namespace Database\Factories;

use App\Models\Contribution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContributionFactory extends Factory
{
    protected $model = Contribution::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['paid', 'unpaid']),
            'remarks' => $this->faker->optional()->sentence(),
        ];
    }
}
