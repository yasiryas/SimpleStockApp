<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div x-data="dashboardFilter()">
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm font-medium text-gray-500">Total Produk</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $products->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm font-medium text-gray-500">Total Stok</p>
                <p class="text-2xl font-bold text-gray-900 mt-1" x-text="totalStock"></p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm font-medium text-gray-500">Stok Menipis</p>
                <p class="text-2xl font-bold text-red-600 mt-1" x-text="lowStockCount"></p>
            </div>
        </div>

        {{-- Products Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Stok Produk</h2>
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="search" placeholder="Cari produk..."
                           class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 w-56">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left">
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">SKU</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Satuan</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="product in products" :key="product.id">
                            <tr x-show="matches(product.nama, product.sku)"
                                class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs text-gray-500" x-text="product.sku"></td>
                                <td class="px-5 py-3.5 font-medium text-gray-900" x-text="product.nama"></td>
                                <td class="px-5 py-3.5 text-gray-500" x-text="product.satuan"></td>
                                <td class="px-5 py-3.5">
                                    <span :data-stock-id="product.id"
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-semibold transition-all duration-300"
                                          :class="product.stok_saat_ini > 5 ? 'bg-indigo-50 text-indigo-700' : 'bg-red-50 text-red-700'"
                                          x-text="product.stok_saat_ini"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="products.length === 0 || filteredProducts.length === 0">
                            <td colspan="4" class="px-5 py-10 text-center text-gray-400">Belum ada produk.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function dashboardFilter() {
            return {
                search: '',
                products: [],
                totalStock: 0,
                lowStockCount: 0,
                pollingInterval: null,
                pollIntervalMs: 5000,

                async init() {
                    await this.fetchStock();
                    this.startPolling();
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

                startPolling() {
                    this.pollingInterval = setInterval(() => this.fetchStock(), this.pollIntervalMs);
                },

                stopPolling() {
                    if (this.pollingInterval) clearInterval(this.pollingInterval);
                },

                get filteredProducts() {
                    if (!this.search) return this.products;
                    const q = this.search.toLowerCase();
                    return this.products.filter(p => p.nama.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
                },

                matches(nama, sku) {
                    if (!this.search) return true;
                    const q = this.search.toLowerCase();
                    return nama.toLowerCase().includes(q) || sku.toLowerCase().includes(q);
                }
            }
        }
    </script>
</x-app-layout>
