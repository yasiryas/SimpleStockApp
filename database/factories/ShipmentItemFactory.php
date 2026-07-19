<?php

namespace Database\Factories;

use App\Models\ShipmentItem;
use App\Models\Shipment;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentItemFactory extends Factory
{
    protected $model = ShipmentItem::class;

    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'product_id' => Product::factory(),
            'qty' => $this->faker->numberBetween(1, 20),
        ];
    }
}
