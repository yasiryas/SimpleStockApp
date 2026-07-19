<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockReturn;
use App\Exports\ReturnsExport;
use App\Events\StockUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = StockReturn::with('product', 'shipment', 'user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
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

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $returns = $query->paginate(15)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'returns' => $returns->items(),
                'pagination' => [
                    'current_page' => $returns->currentPage(),
                    'last_page' => $returns->lastPage(),
                    'total' => $returns->total(),
                    'per_page' => $returns->perPage(),
                    'from' => $returns->firstItem(),
                    'to' => $returns->lastItem(),
                ],
            ]);
        }

        return view('returns', [
            'returns'  => $returns,
            'products' => Product::orderBy('nama')->get(),
            'shipments' => \App\Models\Shipment::orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new ReturnsExport($request), 'returns-' . date('Y-m-d') . '.xlsx');
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
