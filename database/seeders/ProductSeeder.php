<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Sembako (BRG-000001 - BRG-000015)
            ['sku' => 'BRG-000001', 'nama' => 'Beras Premium 5kg',          'satuan' => 'Karung', 'stok_saat_ini' => 50],
            ['sku' => 'BRG-000002', 'nama' => 'Beras Medium 5kg',           'satuan' => 'Karung', 'stok_saat_ini' => 40],
            ['sku' => 'BRG-000003', 'nama' => 'Gula Pasir Premium 1kg',     'satuan' => 'Kg',     'stok_saat_ini' => 120],
            ['sku' => 'BRG-000004', 'nama' => 'Gula Pasir Lokal 1kg',       'satuan' => 'Kg',     'stok_saat_ini' => 80],
            ['sku' => 'BRG-000005', 'nama' => 'Minyak Goreng Sunco 2L',     'satuan' => 'Liter',  'stok_saat_ini' => 60],
            ['sku' => 'BRG-000006', 'nama' => 'Minyak Goreng Sania 2L',     'satuan' => 'Liter',  'stok_saat_ini' => 75],
            ['sku' => 'BRG-000007', 'nama' => 'Minyak Goreng Bimoli 2L',    'satuan' => 'Liter',  'stok_saat_ini' => 45],
            ['sku' => 'BRG-000008', 'nama' => 'Telur Ayam Negeri 1kg',      'satuan' => 'Kg',     'stok_saat_ini' => 200],
            ['sku' => 'BRG-000009', 'nama' => 'Telur Ayam Kampung 1kg',     'satuan' => 'Kg',     'stok_saat_ini' => 30],
            ['sku' => 'BRG-000010', 'nama' => 'Tepung Terigu Segitiga 1kg', 'satuan' => 'Kg',     'stok_saat_ini' => 90],
            ['sku' => 'BRG-000011', 'nama' => 'Tepung Terigu Kunci 1kg',    'satuan' => 'Kg',     'stok_saat_ini' => 55],
            ['sku' => 'BRG-000012', 'nama' => 'Tepung Beras Rosebrand 1kg', 'satuan' => 'Kg',     'stok_saat_ini' => 25],
            ['sku' => 'BRG-000013', 'nama' => 'Kopi Bubuk Kapal Api 200g',  'satuan' => 'Pcs',    'stok_saat_ini' => 3],
            ['sku' => 'BRG-000014', 'nama' => 'Kopi Bubuk Good Day 200g',   'satuan' => 'Pcs',    'stok_saat_ini' => 15],
            ['sku' => 'BRG-000015', 'nama' => 'Susu Kental Manis Frisian Flag', 'satuan' => 'Kaleng', 'stok_saat_ini' => 2],

            // Makanan Ringan & Minuman (BRG-000016 - BRG-000030)
            ['sku' => 'BRG-000016', 'nama' => 'Mie Instan Indomie Goreng',  'satuan' => 'Dus',    'stok_saat_ini' => 35],
            ['sku' => 'BRG-000017', 'nama' => 'Mie Instan Indomie Kuah',    'satuan' => 'Dus',    'stok_saat_ini' => 40],
            ['sku' => 'BRG-000018', 'nama' => 'Mie Instan Sarimi',          'satuan' => 'Dus',    'stok_saat_ini' => 20],
            ['sku' => 'BRG-000019', 'nama' => 'Biskuit Roma Kelapa',        'satuan' => 'Pcs',    'stok_saat_ini' => 60],
            ['sku' => 'BRG-000020', 'nama' => 'Biskuit Roma Sandwich',      'satuan' => 'Pcs',    'stok_saat_ini' => 45],
            ['sku' => 'BRG-000021', 'nama' => 'Biskuit Khong Guan',         'satuan' => 'Kaleng', 'stok_saat_ini' => 25],
            ['sku' => 'BRG-000022', 'nama' => 'Wafer Tango',                'satuan' => 'Pcs',    'stok_saat_ini' => 50],
            ['sku' => 'BRG-000023', 'nama' => 'Air Mineral Aqua Galon',     'satuan' => 'Galon',  'stok_saat_ini' => 15],
            ['sku' => 'BRG-000024', 'nama' => 'Air Mineral Le Minerale 600ml', 'satuan' => 'Dus', 'stok_saat_ini' => 30],
            ['sku' => 'BRG-000025', 'nama' => 'Teh Botol Sosro 500ml',      'satuan' => 'Botol',  'stok_saat_ini' => 40],
            ['sku' => 'BRG-000026', 'nama' => 'Teh Kotak Ultra 500ml',      'satuan' => 'Botol',  'stok_saat_ini' => 35],
            ['sku' => 'BRG-000027', 'nama' => 'Susu UHT Ultra Milk 1L',     'satuan' => 'Liter',  'stok_saat_ini' => 20],
            ['sku' => 'BRG-000028', 'nama' => 'Susu UHT Diamond 1L',       'satuan' => 'Liter',  'stok_saat_ini' => 18],
            ['sku' => 'BRG-000029', 'nama' => 'Saos Sambal Indofood 500ml', 'satuan' => 'Botol',  'stok_saat_ini' => 1],
            ['sku' => 'BRG-000030', 'nama' => 'Kecap Manis Bango 500ml',    'satuan' => 'Botol',  'stok_saat_ini' => 12],

            // Bumbu & Bahan Masak (BRG-000031 - BRG-000040)
            ['sku' => 'BRG-000031', 'nama' => 'Garam Dapur Refina 500g',    'satuan' => 'Pcs',    'stok_saat_ini' => 100],
            ['sku' => 'BRG-000032', 'nama' => 'Penyedap Masako 250g',       'satuan' => 'Pcs',    'stok_saat_ini' => 65],
            ['sku' => 'BRG-000033', 'nama' => 'Penyedap Royco 250g',        'satuan' => 'Pcs',    'stok_saat_ini' => 55],
            ['sku' => 'BRG-000034', 'nama' => 'Merica Bubuk Ladaku 50g',    'satuan' => 'Pcs',    'stok_saat_ini' => 40],
            ['sku' => 'BRG-000035', 'nama' => 'Santan Kara 200ml',          'satuan' => 'Pcs',    'stok_saat_ini' => 70],
            ['sku' => 'BRG-000036', 'nama' => 'Margarin Blueband 200g',     'satuan' => 'Pcs',    'stok_saat_ini' => 35],
            ['sku' => 'BRG-000037', 'nama' => 'Minyak Goreng Curah 1L',     'satuan' => 'Liter',  'stok_saat_ini' => 4],
            ['sku' => 'BRG-000038', 'nama' => 'Kacang Hijau 1kg',           'satuan' => 'Kg',     'stok_saat_ini' => 20],
            ['sku' => 'BRG-000039', 'nama' => 'Ketumbar Bubuk 100g',        'satuan' => 'Pcs',    'stok_saat_ini' => 25],
            ['sku' => 'BRG-000040', 'nama' => 'Bawang Goreng 200g',         'satuan' => 'Pcs',    'stok_saat_ini' => 3],

            // Perawatan Rumah & Kebersihan (BRG-000041 - BRG-000050)
            ['sku' => 'BRG-000041', 'nama' => 'Sabun Mandi Lifebuoy 75g',   'satuan' => 'Pcs',    'stok_saat_ini' => 80],
            ['sku' => 'BRG-000042', 'nama' => 'Sabun Mandi Lux 75g',        'satuan' => 'Pcs',    'stok_saat_ini' => 60],
            ['sku' => 'BRG-000043', 'nama' => 'Pasta Gigi Pepsodent 120g',  'satuan' => 'Pcs',    'stok_saat_ini' => 45],
            ['sku' => 'BRG-000044', 'nama' => 'Sampo Clear 100ml',          'satuan' => 'Botol',  'stok_saat_ini' => 40],
            ['sku' => 'BRG-000045', 'nama' => 'Sampo Sunsilk 100ml',        'satuan' => 'Botol',  'stok_saat_ini' => 35],
            ['sku' => 'BRG-000046', 'nama' => 'Sabun Cuci Piring Sunlight', 'satuan' => 'Botol',  'stok_saat_ini' => 55],
            ['sku' => 'BRG-000047', 'nama' => 'Sabun Cuci Rinso 900g',      'satuan' => 'Pcs',    'stok_saat_ini' => 25],
            ['sku' => 'BRG-000048', 'nama' => 'Pembersih Lantai So Klin',   'satuan' => 'Botol',  'stok_saat_ini' => 30],
            ['sku' => 'BRG-000049', 'nama' => 'Pemutih Bayclin 500ml',      'satuan' => 'Botol',  'stok_saat_ini' => 2],
            ['sku' => 'BRG-000050', 'nama' => 'Pengharum Ruangan Stella',   'satuan' => 'Pcs',    'stok_saat_ini' => 20],

            // Tambahan (BRG-000051 - BRG-000055)
            ['sku' => 'BRG-000051', 'nama' => 'Tisu Toilet Nice 10 roll',   'satuan' => 'Pack',   'stok_saat_ini' => 30],
            ['sku' => 'BRG-000052', 'nama' => 'Minyak Kayu Putih Cap Lang', 'satuan' => 'Botol',  'stok_saat_ini' => 15],
            ['sku' => 'BRG-000053', 'nama' => 'Obat Nyamuk Vape Matik',     'satuan' => 'Pcs',    'stok_saat_ini' => 10],
            ['sku' => 'BRG-000054', 'nama' => 'Kantong Plastik 1kg',        'satuan' => 'Pack',   'stok_saat_ini' => 100],
            ['sku' => 'BRG-000055', 'nama' => 'Kardus Packing 40x30',       'satuan' => 'Pcs',    'stok_saat_ini' => 4],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
