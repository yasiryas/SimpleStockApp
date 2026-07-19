<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shipment;
use App\Models\StockMovement;
use App\Models\StockReturn;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $products = Product::select('id', 'sku', 'nama', 'satuan', 'stok_saat_ini')->get();

        $recentMovements = StockMovement::with('product')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($m) => [
                'id'         => $m->id,
                'created_at' => $m->created_at->locale('id')->translatedFormat('d F H:i'),
                'product'    => $m->product?->nama ?? '-',
                'tipe'       => $m->tipe,
                'qty'        => $m->qty,
            ]);

        return response()->json([
            'total_products'  => $products->count(),
            'total_stock'     => $products->sum('stok_saat_ini'),
            'low_stock_count' => $products->where('stok_saat_ini', '<=', 5)->count(),
            'active_shipments'=> Shipment::whereIn('status', ['draft', 'dikirim'])->count(),
            'pending_returns' => StockReturn::where('status', 'pending')->count(),
            'recent_movements'=> $recentMovements,
        ]);
    }
}
