<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'sku' => 'BRG-' . str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'nama' => $this->faker->word(),
            'satuan' => $this->faker->randomElement(['Pcs', 'Kg', 'Liter', 'Botol', 'Dus', 'Karung', 'Kaleng']),
            'stok_saat_ini' => $this->faker->numberBetween(0, 200),
        ];
    }
}
