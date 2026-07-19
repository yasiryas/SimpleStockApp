<?php

namespace App\Exports;

use App\Models\Shipment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ShipmentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Shipment::with('items.product', 'user')->latest();

        if ($this->request && $this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_shipment', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['No. Shipment', 'Tujuan', 'Status', 'Jumlah Item', 'Total Qty', 'No. Resi', 'User', 'Catatan', 'Dibuat Pada'];
    }

    public function map($s): array
    {
        return [
            $s->no_shipment,
            $s->tujuan ?? '-',
            strtoupper($s->status),
            $s->items->count(),
            $s->items->sum('qty'),
            $s->no_resi ?? '-',
            $s->user->name ?? '-',
            $s->catatan ?? '-',
            $s->created_at->format('d/m/Y H:i'),
        ];
    }
}