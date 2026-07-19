<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@mail.com')->first();
        $products = Product::all();

        $shipment1 = Shipment::create([
            'no_shipment' => 'SHIP-001',
            'no_resi'     => 'RESI-JNE-001',
            'tujuan'      => 'Toko Cabang Merdeka',
            'user_id'     => $admin->id,
            'catatan'     => 'Pengiriman rutin bulanan',
            'status'      => 'selesai',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'product_id'  => $products[0]->id,
            'qty'         => 5,
        ]);
        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'product_id'  => $products[2]->id,
            'qty'         => 10,
        ]);
        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'product_id'  => $products[4]->id,
            'qty'         => 8,
        ]);

        $shipment2 = Shipment::create([
            'no_shipment' => 'SHIP-002',
            'no_resi'     => 'RESI-SICEPAT-002',
            'tujuan'      => 'Gudang Pusat Surabaya',
            'user_id'     => $admin->id,
            'catatan'     => 'Pengiriman stok tambahan',
            'status'      => 'dikirim',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment2->id,
            'product_id'  => $products[1]->id,
            'qty'         => 15,
        ]);
        ShipmentItem::create([
            'shipment_id' => $shipment2->id,
            'product_id'  => $products[6]->id,
            'qty'         => 20,
        ]);

        $shipment3 = Shipment::create([
            'no_shipment' => 'SHIP-003',
            'no_resi'     => null,
            'tujuan'      => 'Toko Cabang Diponegoro',
            'user_id'     => $admin->id,
            'catatan'     => 'Menunggu persetujuan',
            'status'      => 'draft',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment3->id,
            'product_id'  => $products[3]->id,
            'qty'         => 25,
        ]);
        ShipmentItem::create([
            'shipment_id' => $shipment3->id,
            'product_id'  => $products[5]->id,
            'qty'         => 12,
        ]);
        ShipmentItem::create([
            'shipment_id' => $shipment3->id,
            'product_id'  => $products[7]->id,
            'qty'         => 6,
        ]);
        ShipmentItem::create([
            'shipment_id' => $shipment3->id,
            'product_id'  => $products[8]->id,
            'qty'         => 3,
        ]);
    }
}
