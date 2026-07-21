<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'nama',
        'satuan',
        'stok_saat_ini',
    ];

    protected $casts = [
        'stok_saat_ini' => 'integer',
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function shipmentItems()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function stockReturns()
    {
        return $this->hasMany(StockReturn::class);
    }

    public function getSkuNamaAttribute(): string
    {
        return "{$this->sku} - {$this->nama}";
    }

    public function getSkuNamaStokAttribute(): string
    {
        $stokLabel = $this->stok_saat_ini <= 5 
            ? "<span class='text-red-600 font-medium'>Stok: {$this->stok_saat_ini}</span>"
            : "Stok: {$this->stok_saat_ini}";
        return "{$this->sku} - {$this->nama} ({$stokLabel})";
    }
}
