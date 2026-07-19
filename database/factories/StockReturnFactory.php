<?php

namespace Database\Factories;

use App\Models\StockReturn;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockReturnFactory extends Factory
{
    protected $model = StockReturn::class;

    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'product_id' => Product::factory(),
            'qty' => $this->faker->numberBetween(1, 10),
            'alasan' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['pending', 'disetujui', 'ditolak']),
            'user_id' => User::factory(),
        ];
    }
}
