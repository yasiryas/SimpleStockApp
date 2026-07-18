<?php

namespace App\Http\Controllers;

use App\Events\StockUpdated;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'no_shipment' => ['required', 'string', 'max:255', 'unique:shipments,no_shipment'],
            'tujuan'      => ['nullable', 'string', 'max:255'],
            'no_resi'     => ['nullable', 'string', 'max:255'],
            'catatan'     => ['nullable', 'string', 'max:1000'],
            'items'       => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty'        => ['required', 'integer', 'min:1'],
        ]);

        Shipment::create([
            'no_shipment' => $validated['no_shipment'],
            'tujuan'      => $validated['tujuan'] ?? null,
            'no_resi'     => $validated['no_resi'] ?? null,
            'user_id'     => Auth::id(),
            'catatan'     => $validated['catatan'] ?? null,
            'status'      => 'draft',
        ]);

        $shipment = Shipment::where('no_shipment', $validated['no_shipment'])->first();

        foreach ($validated['items'] as $item) {
            ShipmentItem::create([
                'shipment_id' => $shipment->id,
                'product_id'  => $item['product_id'],
                'qty'         => $item['qty'],
            ]);
        }

        return redirect()->back()->with('success', 'Shipment berhasil dibuat (draft).');
    }

    public function markSent(Shipment $shipment): RedirectResponse
    {
        if ($shipment->status !== 'draft') {
            return redirect()->back()->with('error', 'Hanya shipment dengan status draft yang bisa dikirim.');
        }

        DB::transaction(function () use ($shipment) {
            foreach ($shipment->items as $item) {
                $product = $item->product;

                if ($product->stok_saat_ini < $item->qty) {
                    abort(422, "Stok {$product->nama} tidak mencukupi.");
                }

                $product->decrement('stok_saat_ini', $item->qty);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'tipe'       => 'out',
                    'qty'        => $item->qty,
                    'referensi'  => $shipment->no_shipment,
                    'user_id'    => Auth::id(),
                    'catatan'    => "Shipment: {$shipment->no_shipment}",
                ]);

                StockUpdated::dispatch(
                    $product->id,
                    $product->fresh()->stok_saat_ini,
                    'out',
                    $item->qty,
                );
            }

            $shipment->update(['status' => 'dikirim']);
        });

        return redirect()->back()->with('success', 'Shipment telah dikirim dan stok dikurangi.');
    }

    public function markDone(Shipment $shipment): RedirectResponse
    {
        if ($shipment->status !== 'dikirim') {
            return redirect()->back()->with('error', 'Hanya shipment dengan status dikirim yang bisa diselesaikan.');
        }

        $shipment->update(['status' => 'selesai']);

        return redirect()->back()->with('success', 'Shipment selesai.');
    }
}
