<?php

namespace App\Exports;

use App\Models\StockReturn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReturnsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = StockReturn::with('product', 'shipment', 'user')->latest();

        if ($this->request && $this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('nama', 'like', "%{$search}%")
                       ->orWhere('sku', 'like', "%{$search}%");
                })
                ->orWhere('alasan', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereHas('shipment', function ($sq) use ($search) {
                    $sq->where('no_shipment', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                });
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'SKU', 'Nama Produk', 'Qty', 'Shipment', 'Alasan', 'Status', 'User'];
    }

    public function map($r): array
    {
        return [
            $r->created_at->format('d/m/Y H:i'),
            $r->product->sku ?? '-',
            $r->product->nama ?? '-',
            $r->qty,
            $r->shipment->no_shipment ?? '-',
            $r->alasan,
            strtoupper($r->status),
            $r->user->name ?? '-',
        ];
    }
}