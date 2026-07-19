<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div x-data="dashboardApp()" x-init="init()">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-500">Total Produk</p>
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-2">{{ $products->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-500">Total Stok</p>
                    <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-2" x-text="totalStock.toLocaleString('id-ID')">{{ $total_stock }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-500">Stok Menipis</p>
                    <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-red-600 mt-2" x-text="lowStockCount">{{ $low_stock_count }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-500">Pengiriman Aktif</p>
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-1m0 0l2 1m-2-1v10a1 1 0 002 1h12a1 1 0 002-1v-9a1 1 0 00-1-1h-5m-8 4h12"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-blue-600 mt-2" x-text="activeShipments"></p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-500">Retur Pending</p>
                    <div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-yellow-600 mt-2" x-text="pendingReturns">{{ $pending_returns }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Mutasi Stok Terbaru</h2>
                    <a href="{{ route('stock.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-left bg-gray-50">
                                <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Tanggal</th>
                                <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Produk</th>
                                <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Tipe</th>
                                <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($recent_movements as $m)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $m->created_at->locale('id')->translatedFormat('d F H:i') }}</td>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $m->product->nama ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $m->tipe === 'in' ? 'bg-green-50 text-green-700' : ($m->tipe === 'out' ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700') }}">
                                        {{ $m->tipe === 'in' ? 'MASUK' : ($m->tipe === 'out' ? 'KELUAR' : 'RETUR') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-mono text-gray-900">{{ $m->qty }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-gray-400">Belum ada mutasi stok.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Stok Menipis</h2>
                    <a href="{{ route('products.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Kelola Produk</a>
                </div>
                <div x-show="lowStockProducts.length > 0">
                    <template x-for="product in lowStockProducts" :key="product.id">
                        <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-b-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="product.nama"></p>
                                    <p class="text-xs text-gray-400" x-text="product.sku + ' (' + product.satuan + ')'"></p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-semibold bg-red-50 text-red-700 shrink-0 ml-3" x-text="product.stok_saat_ini"></span>
                        </div>
                    </template>
                </div>
                <div x-show="lowStockProducts.length === 0" class="px-5 py-10 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Semua stok mencukupi
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="font-semibold text-gray-900">Semua Produk</h2>
                <div class="relative w-full sm:w-auto">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="search" @input.debounce.300ms="fetchPaginated(1)" placeholder="Cari produk..."
                           class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 w-full sm:w-56">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left bg-gray-50">
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">SKU</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Satuan</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="product in paginatedProducts" :key="product.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs text-gray-500" x-text="product.sku"></td>
                                <td class="px-5 py-3.5 font-medium text-gray-900" x-text="product.nama"></td>
                                <td class="px-5 py-3.5 text-gray-500" x-text="product.satuan"></td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-semibold"
                                          :class="product.stok_saat_ini > 5 ? 'bg-indigo-50 text-indigo-700' : 'bg-red-50 text-red-700'"
                                          x-text="product.stok_saat_ini"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="paginatedProducts.length === 0">
                            <td colspan="4" class="px-5 py-10 text-center text-gray-400">Produk tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <template x-if="pagination && pagination.last_page > 1">
                <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
                    <div class="text-sm text-gray-500" x-text="'Menampilkan ' + pagination.from + ' - ' + pagination.to + ' dari ' + pagination.total + ' produk'"></div>
                    <div class="flex gap-1">
                        <button @click="fetchPaginated(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                                class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">Sebelumnya</button>
                        <template x-for="page in paginationPages" :key="page">
                            <button @click="fetchPaginated(page)"
                                    class="w-8 h-8 text-sm font-medium rounded-lg transition-colors"
                                    :class="page === pagination.current_page ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                                    x-text="page"></button>
                        </template>
                        <button @click="fetchPaginated(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                                class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">Selanjutnya</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
        function dashboardApp() {
            return {
                search: '',
                products: [],
                paginatedProducts: [],
                pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
                totalStock: {{ $total_stock }},
                lowStockCount: {{ $low_stock_count }},
                activeShipments: {{ $shipment_draft + $shipment_sent }},
                pendingReturns: {{ $pending_returns }},
                pollingInterval: null,

                init() {
                    this.fetchStock();
                    this.fetchPaginated(1);
                    this.pollingInterval = setInterval(() => this.fetchStock(), 5000);
                },

                async fetchStock() {
                    try {
                        const response = await fetch('{{ route('products.stock') }}');
                        const data = await response.json();
                        this.products = Object.entries(data.products).map(([id, info]) => ({
                            id: parseInt(id),
                            stok_saat_ini: info.stok_saat_ini,
                            nama: info.nama,
                            sku: info.sku,
                            satuan: info.satuan,
                        }));
                        this.totalStock = this.products.reduce((sum, p) => sum + p.stok_saat_ini, 0);
                        this.lowStockCount = this.products.filter(p => p.stok_saat_ini <= 5).length;
                    } catch (e) {
                        console.error('Failed to fetch stock:', e);
                    }
                },

                async fetchPaginated(page) {
                    try {
                        const params = new URLSearchParams();
                        if (this.search) params.set('search', this.search);
                        params.set('page', page);

                        const response = await fetch('{{ route('dashboard.products') }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();
                        this.paginatedProducts = data.products;
                        this.pagination = data.pagination;
                    } catch (e) {
                        console.error('Failed to fetch paginated products:', e);
                    }
                },

                get lowStockProducts() {
                    return this.products.filter(p => p.stok_saat_ini <= 5);
                },

                get paginationPages() {
                    if (!this.pagination) return [];
                    const pages = [];
                    const start = Math.max(1, this.pagination.current_page - 2);
                    const end = Math.min(this.pagination.last_page, start + 4);
                    for (let i = start; i <= end; i++) pages.push(i);
                    return pages;
                }
            }
        }
    </script>
</x-app-layout>