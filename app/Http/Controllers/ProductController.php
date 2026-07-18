<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('products', [
            'products' => Product::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku'    => ['required', 'string', 'max:255', 'unique:products,sku'],
            'nama'   => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:50'],
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'sku'    => ['required', 'string', 'max:255', 'unique:products,sku,' . $product->id],
            'nama'   => ['required', 'string', 'max:255'],
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
