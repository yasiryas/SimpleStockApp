<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@mail.com')->first();
        $products = Product::all();

        foreach ($products as $product) {
            StockMovement::create([
                'product_id' => $product->id,
                'tipe'       => 'in',
                'qty'        => $product->stok_saat_ini,
                'referensi'  => 'STOK-AWAL',
                'user_id'    => $admin->id,
                'catatan'    => 'Stok awal ' . $product->nama,
            ]);
        }

        StockMovement::create([
            'product_id' => $products[0]->id,
            'tipe'       => 'out',
            'qty'        => 5,
            'referensi'  => 'PENGIRIMAN-001',
            'user_id'    => $admin->id,
            'catatan'    => 'Pengiriman ke Toko Cabang',
        ]);

        StockMovement::create([
            'product_id' => $products[2]->id,
            'tipe'       => 'out',
            'qty'        => 10,
            'referensi'  => 'PENGIRIMAN-002',
            'user_id'    => $admin->id,
            'catatan'    => 'Pengiriman ke Gudang Penyimpanan',
        ]);

        StockMovement::create([
            'product_id' => $products[1]->id,
            'tipe'       => 'in',
            'qty'        => 50,
            'referensi'  => 'PO-2024-001',
            'user_id'    => $admin->id,
            'catatan'    => 'Pembelian dari Supplier',
        ]);

        StockMovement::create([
            'product_id' => $products[3]->id,
            'tipe'       => 'out',
            'qty'        => 3,
            'referensi'  => 'RETUR-001',
            'user_id'    => $admin->id,
            'catatan'    => 'Retur barang rusak',
        ]);
    }
}
