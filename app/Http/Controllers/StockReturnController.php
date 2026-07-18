<?php

namespace App\Http\Controllers;

use App\Events\StockUpdated;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockReturn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockReturnController extends Controller
{
    public function index()
    {
        return view('returns', [
            'returns'  => StockReturn::with('product', 'shipment', 'user')->latest()->get(),
            'products' => Product::orderBy('nama')->get(),
            'shipments' => \App\Models\Shipment::orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'  => ['required', 'exists:products,id'],
            'qty'         => ['required', 'integer', 'min:1'],
            'alasan'      => ['required', 'string', 'max:1000'],
            'shipment_id' => ['nullable', 'exists:shipments,id'],
        ]);

        StockReturn::create([
            'product_id'  => $validated['product_id'],
            'qty'         => $validated['qty'],
            'alasan'      => $validated['alasan'],
            'shipment_id' => $validated['shipment_id'] ?? null,
            'user_id'     => Auth::id(),
            'status'      => 'pending',
        ]);

        return redirect()->route('returns.index')->with('success', 'Retur diajukan (pending).');
    }

    public function approve(StockReturn $return): RedirectResponse
    {
        if ($return->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya retur pending yang bisa disetujui.');
        }

        DB::transaction(function () use ($return) {
            $product = $return->product;
            $product->increment('stok_saat_ini', $return->qty);

            StockMovement::create([
                'product_id' => $return->product_id,
                'tipe'       => 'return',
                'qty'        => $return->qty,
                'referensi'  => $return->shipment?->no_shipment ?? 'retur-umum',
                'user_id'    => Auth::id(),
                'catatan'    => "Retur: {$return->alasan}",
            ]);

            StockUpdated::dispatch(
                $product->id,
                $product->fresh()->stok_saat_ini,
                'return',
                $return->qty,
            );

            $return->update(['status' => 'disetujui']);
        });

        return redirect()->route('returns.index')->with('success', 'Retur disetujui, stok bertambah.');
    }

    public function reject(StockReturn $return): RedirectResponse
    {
        if ($return->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya retur pending yang bisa ditolak.');
        }

        $return->update(['status' => 'ditolak']);

        return redirect()->route('returns.index')->with('success', 'Retur ditolak.');
    }
}
