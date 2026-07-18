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
}
