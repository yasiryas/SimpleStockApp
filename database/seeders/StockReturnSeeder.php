<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shipment;
use App\Models\StockReturn;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockReturnSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@mail.com')->first();
        $products = Product::all();
        $shipment1 = Shipment::where('no_shipment', 'SHIP-001')->first();

        StockReturn::create([
            'shipment_id' => $shipment1->id,
            'product_id'  => $products[0]->id,
            'qty'         => 1,
            'alasan'      => 'Barang pecah saat pengiriman',
            'status'      => 'disetujui',
            'user_id'     => $admin->id,
        ]);

        StockReturn::create([
            'shipment_id' => $shipment1->id,
            'product_id'  => $products[2]->id,
            'qty'         => 2,
            'alasan'      => 'Kualitas tidak sesuai',
            'status'      => 'pending',
            'user_id'     => $admin->id,
        ]);

        StockReturn::create([
            'shipment_id' => null,
            'product_id'  => $products[4]->id,
            'qty'         => 1,
            'alasan'      => 'Produk mendekati kadaluarsa',
            'status'      => 'ditolak',
            'user_id'     => $admin->id,
        ]);
    }
}
