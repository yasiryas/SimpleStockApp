<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockReturnController;
use App\Http\Controllers\UserController;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $products = Product::latest()->get();
    return view('dashboard', [
        'products'        => $products,
        'total_stock'     => $products->sum('stok_saat_ini'),
        'low_stock_count' => $products->where('stok_saat_ini', '<=', 5)->count(),
        'shipment_draft'  => Shipment::where('status', 'draft')->count(),
        'shipment_sent'   => Shipment::where('status', 'dikirim')->count(),
        'shipment_done'   => Shipment::where('status', 'selesai')->count(),
        'pending_returns' => \App\Models\StockReturn::where('status', 'pending')->count(),
        'recent_movements'=> StockMovement::with('product', 'user')->latest()->take(5)->get(),
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

    // Stock API (AJAX polling)
    Route::get('/products/stock', [ProductController::class, 'getStock'])->name('products.stock');
    Route::get('/dashboard/products', [ProductController::class, 'dashboardProducts'])->name('dashboard.products');

    // Stock mutations
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::post('/stock/in',     [StockController::class, 'storeStockIn'])->name('stock.in');
    Route::post('/stock/out',    [StockController::class, 'storeStockOut'])->name('stock.out');
    // Shipments
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::post('/shipment', [ShipmentController::class, 'store'])->name('shipment.store');
    Route::post('/shipment/{shipment}/mark-sent',     [ShipmentController::class, 'markSent'])->name('shipment.mark-sent');
    Route::post('/shipment/{shipment}/mark-done',     [ShipmentController::class, 'markDone'])->name('shipment.mark-done');

    // Returns
    Route::get('/returns',  [StockReturnController::class, 'index'])->name('returns.index');
    Route::post('/return',  [StockReturnController::class, 'store'])->name('return.store');
    Route::post('/return/{return}/approve', [StockReturnController::class, 'approve'])->name('return.approve');
    Route::post('/return/{return}/reject',  [StockReturnController::class, 'reject'])->name('return.reject');

    // User Management (admin only)
    Route::middleware('can:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    });

    // Products export
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');

    // Stock export
    Route::get('/stock/export', [StockController::class, 'export'])->name('stock.export');

    // Shipments export
    Route::get('/shipments/export', [ShipmentController::class, 'export'])->name('shipments.export');

    // Returns export
    Route::get('/returns/export', [StockReturnController::class, 'export'])->name('returns.export');
});

require __DIR__.'/auth.php';

