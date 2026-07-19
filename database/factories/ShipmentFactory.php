<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'no_shipment' => 'SHIP-' . strtoupper($this->faker->unique()->bothify('???-#####')),
            'no_resi' => $this->faker->optional(0.7)->bothify('RESI-########'),
            'tujuan' => $this->faker->city(),
            'user_id' => User::factory(),
            'catatan' => $this->faker->optional(0.5)->sentence(),
            'status' => $this->faker->randomElement(['draft', 'dikirim', 'selesai']),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn() => ['status' => 'draft']);
    }

    public function dikirim(): static
    {
        return $this->state(fn() => ['status' => 'dikirim']);
    }

    public function selesai(): static
    {
        return $this->state(fn() => ['status' => 'selesai']);
    }
}
