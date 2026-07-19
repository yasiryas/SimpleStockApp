<?php

namespace Database\Factories;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'tipe' => $this->faker->randomElement(['in', 'out']),
            'qty' => $this->faker->numberBetween(1, 50),
            'referensi' => $this->faker->optional(0.7)->bothify('REF-####'),
            'user_id' => User::factory(),
            'catatan' => $this->faker->optional(0.5)->sentence(),
        ];
    }
}
