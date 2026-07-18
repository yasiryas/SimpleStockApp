<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockReturnController;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'products' => Product::latest()->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Products CRUD
    Route::get('/products',           [ProductController::class, 'index'])->name('products.index');
    Route::post('/products',          [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}',  [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Stock mutations
    Route::get('/stock', function () {
        return view('stock', [
            'products'  => Product::orderBy('nama')->get(),
            'movements' => StockMovement::with('product', 'user')->latest()->paginate(20),
        ]);
    })->name('stock.index');
    Route::post('/stock/in',     [StockController::class, 'storeStockIn'])->name('stock.in');
    Route::post('/stock/out',    [StockController::class, 'storeStockOut'])->name('stock.out');
    // Shipments
    Route::get('/shipments', function () {
        return view('shipments', [
            'products'  => Product::orderBy('nama')->get(),
            'shipments' => Shipment::with('items.product', 'user')->latest()->get(),
        ]);
    })->name('shipments.index');
    Route::post('/shipment', [ShipmentController::class, 'store'])->name('shipment.store');
    Route::post('/shipment/{shipment}/mark-sent',     [ShipmentController::class, 'markSent'])->name('shipment.mark-sent');
    Route::post('/shipment/{shipment}/mark-done',     [ShipmentController::class, 'markDone'])->name('shipment.mark-done');

    // Returns
    Route::get('/returns',  [StockReturnController::class, 'index'])->name('returns.index');
    Route::post('/return',  [StockReturnController::class, 'store'])->name('return.store');
    Route::post('/return/{return}/approve', [StockReturnController::class, 'approve'])->name('return.approve');
    Route::post('/return/{return}/reject',  [StockReturnController::class, 'reject'])->name('return.reject');
});

require __DIR__.'/auth.php';
