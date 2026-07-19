<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'no_shipment',
        'no_resi',
        'tujuan',
        'user_id',
        'catatan',
        'status',
    ];

    protected $appends = ['items_count', 'total_qty'];

    public function getItemsCountAttribute()
    {
        return $this->items->count();
    }

    public function getTotalQtyAttribute()
    {
        return $this->items->sum('qty');
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockReturns()
    {
        return $this->hasMany(StockReturn::class);
    }
}
