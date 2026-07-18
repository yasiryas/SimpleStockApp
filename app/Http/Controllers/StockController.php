<?php

namespace App\Http\Controllers;

use App\Events\StockUpdated;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function storeStockIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty'        => ['required', 'integer', 'min:1'],
            'referensi'  => ['nullable', 'string', 'max:255'],
            'catatan'    => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        DB::transaction(function () use ($product, $validated) {
            $product->increment('stok_saat_ini', $validated['qty']);

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'tipe'       => 'in',
                'qty'        => $validated['qty'],
                'referensi'  => $validated['referensi'],
                'user_id'    => Auth::id(),
                'catatan'    => $validated['catatan'],
            ]);
        });

        StockUpdated::dispatch($product->id, $product->fresh()->stok_saat_ini, 'in', $validated['qty']);

        return redirect()->back()->with('success', 'Stok berhasil ditambahkan.');
    }

    public function storeStockOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty'        => ['required', 'integer', 'min:1'],
            'referensi'  => ['nullable', 'string', 'max:255'],
            'catatan'    => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stok_saat_ini < $validated['qty']) {
            abort(422, 'Stok tidak mencukupi.');
        }

        DB::transaction(function () use ($product, $validated) {
            $product->decrement('stok_saat_ini', $validated['qty']);

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'tipe'       => 'out',
                'qty'        => $validated['qty'],
                'referensi'  => $validated['referensi'],
                'user_id'    => Auth::id(),
                'catatan'    => $validated['catatan'],
            ]);
        });

        StockUpdated::dispatch($product->id, $product->fresh()->stok_saat_ini, 'out', $validated['qty']);

        return redirect()->back()->with('success', 'Stok berhasil dikeluarkan.');
    }


}
