<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Exports\ProductsExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
            ]);
        }

        return view('products', [
            'products' => $products,
        ]);
    }

    public function getStock(): JsonResponse
    {
        $products = Product::select('id', 'sku', 'nama', 'satuan', 'stok_saat_ini')->get();

        return response()->json([
            'products' => $products->mapWithKeys(fn ($p) => [$p->id => [
                'stok_saat_ini' => $p->stok_saat_ini,
                'nama' => $p->nama,
                'sku' => $p->sku,
                'satuan' => $p->satuan,
            ]]),
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new ProductsExport($request), 'products-' . date('Y-m-d') . '.xlsx');
    }

    private function generateSku(): string
    {
        $latest = Product::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        return 'PRD-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'   => ['required', 'string', 'max:255', 'unique:products,nama'],
            'satuan' => ['required', 'string', 'max:50'],
        ]);

        $validated['sku'] = $this->generateSku();

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'nama'   => ['required', 'string', 'max:255', 'unique:products,nama,' . $product->id],
            'satuan' => ['required', 'string', 'max:50'],
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diubah.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
