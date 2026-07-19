<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockMovementsExport;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with('product', 'user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('nama', 'like', "%{$search}%")
                       ->orWhere('sku', 'like', "%{$search}%");
                })
                ->orWhere('referensi', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(20)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'movements' => $movements->items(),
                'pagination' => [
                    'current_page' => $movements->currentPage(),
                    'last_page' => $movements->lastPage(),
                    'total' => $movements->total(),
                    'per_page' => $movements->perPage(),
                    'from' => $movements->firstItem(),
                    'to' => $movements->lastItem(),
                ],
            ]);
        }

        return view('stock', [
            'products'  => Product::orderBy('nama')->get(),
            'movements' => $movements,
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new StockMovementsExport($request), 'stock-movements-' . date('Y-m-d') . '.xlsx');
    }

    public function storeStockIn(Request $request): RedirectResponse|JsonResponse
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Stok berhasil ditambahkan.']);
        }

        return redirect()->back()->with('success', 'Stok berhasil ditambahkan.');
    }

    public function storeStockOut(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty'        => ['required', 'integer', 'min:1'],
            'referensi'  => ['nullable', 'string', 'max:255'],
            'catatan'    => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stok_saat_ini < $validated['qty']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi. Tersedia: ' . $product->stok_saat_ini], 422);
            }
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Stok berhasil dikeluarkan.']);
        }

        return redirect()->back()->with('success', 'Stok berhasil dikeluarkan.');
    }
}
