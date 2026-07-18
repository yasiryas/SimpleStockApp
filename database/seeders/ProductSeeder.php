<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['sku' => 'BRG-001', 'nama' => 'Beras Premium 5kg',    'satuan' => 'Karung', 'stok_saat_ini' => 50],
            ['sku' => 'BRG-002', 'nama' => 'Gula Pasir 1kg',       'satuan' => 'Kg',     'stok_saat_ini' => 120],
            ['sku' => 'BRG-003', 'nama' => 'Minyak Goreng 2L',     'satuan' => 'Liter',  'stok_saat_ini' => 80],
            ['sku' => 'BRG-004', 'nama' => 'Telur Ayam 1kg',       'satuan' => 'Kg',     'stok_saat_ini' => 200],
            ['sku' => 'BRG-005', 'nama' => 'Tepung Terigu 1kg',    'satuan' => 'Kg',     'stok_saat_ini' => 45],
            ['sku' => 'BRG-006', 'nama' => 'Kopi Bubuk 200g',      'satuan' => 'Pcs',    'stok_saat_ini' => 3],
            ['sku' => 'BRG-007', 'nama' => 'Susu Kental Manis',    'satuan' => 'Kaleng', 'stok_saat_ini' => 2],
            ['sku' => 'BRG-008', 'nama' => 'Mie Instan Dus',       'satuan' => 'Dus',    'stok_saat_ini' => 35],
            ['sku' => 'BRG-009', 'nama' => 'Air Mineral Galon',    'satuan' => 'Galon',  'stok_saat_ini' => 15],
            ['sku' => 'BRG-010', 'nama' => 'Saos Sambal 500ml',    'satuan' => 'Botol',  'stok_saat_ini' => 1],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
