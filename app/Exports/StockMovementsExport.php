<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockMovementsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = StockMovement::with('product', 'user')->latest();

        if ($this->request && $this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('nama', 'like', "%{$search}%")
                       ->orWhere('sku', 'like', "%{$search}%");
                })
                ->orWhere('referensi', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%");
            });
        }

        if ($this->request && $this->request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $this->request->date_from);
        }

        if ($this->request && $this->request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $this->request->date_to);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'SKU', 'Nama Produk', 'Tipe', 'Qty', 'Referensi', 'User', 'Catatan'];
    }

    public function map($m): array
    {
        return [
            $m->created_at->format('d/m/Y H:i'),
            $m->product->sku ?? '-',
            $m->product->nama ?? '-',
            strtoupper($m->tipe),
            $m->qty,
            $m->referensi ?? '-',
            $m->user->name ?? '-',
            $m->catatan ?? '-',
        ];
    }
}