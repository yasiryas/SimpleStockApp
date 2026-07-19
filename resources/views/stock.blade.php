<x-app-layout>
    <x-slot name="header">Mutasi Stok</x-slot>

    <div x-data="stockModal()" class="space-y-4">
        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div class="relative w-full sm:w-auto">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" @input.debounce.300ms="fetchMovements()"
                       placeholder="Cari (Produk, Referensi, Tipe)..."
                       class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 w-64">
            </div>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <button @click="openFilter()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter Tanggal
                </button>
                <a :href="exportUrl"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Excel
                </a>
                <div class="flex gap-3">
                    <button @click="openIn()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Stok Masuk
                    </button>
                    <button @click="openOut()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                        Stok Keluar
                    </button>
                </div>
            </div>
        </div>

        {{-- Active Filter Indicator --}}
        <template x-if="dateFrom || dateTo">
            <div class="flex items-center gap-2 px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-lg">
                <span class="text-sm text-indigo-700">
                    Filter aktif: 
                    <span x-show="dateFrom" class="font-medium" x-text="formatDateOnly(dateFrom)"></span>
                    <span x-show="dateFrom && dateTo" class="mx-1">-</span>
                    <span x-show="dateTo" class="font-medium" x-text="formatDateOnly(dateTo)"></span>
                </span>
                <button @click="clearFilter()" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Hapus</button>
            </div>
        </template>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left bg-gray-50">
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Produk</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Tipe</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Qty</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Referensi</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="m in movements" :key="m.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap" x-text="formatDate(m.created_at)"></td>
                                <td class="px-5 py-3.5 font-medium text-gray-900" x-text="m.product?.nama || '-'"></td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                          :class="m.tipe === 'in' ? 'bg-green-50 text-green-700' : (m.tipe === 'out' ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700')"
                                          x-text="m.tipe === 'in' ? 'MASUK' : (m.tipe === 'out' ? 'KELUAR' : 'RETUR')"></span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-gray-900" x-text="m.qty"></td>
                                <td class="px-5 py-3.5 text-gray-500" x-text="m.referensi || '-'"></td>
                                <td class="px-5 py-3.5 text-gray-500" x-text="m.user?.name || '-'"></td>
                            </tr>
                        </template>
                        <tr x-show="movements.length === 0">
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400">Belum ada mutasi stok.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <template x-if="pagination && pagination.last_page > 1">
            <div class="mt-4 px-5 py-4 border-t border-gray-100 flex items-center justify-between">
                <div class="text-sm text-gray-500" x-text="paginationText"></div>
                <div class="flex gap-1">
                    <button @click="goToPage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">Sebelumnya</button>
                    <template x-for="page in paginationPages" :key="page">
                        <button @click="goToPage(page)"
                                class="w-8 h-8 text-sm font-medium rounded-lg transition-colors"
                                :class="page === pagination.current_page ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                                x-text="page"></button>
                    </template>
                    <button @click="goToPage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">Selanjutnya</button>
                </div>
            </div>
        </template>

        {{-- Modal Stok In / Out / Return --}}
        <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-white/10 backdrop-blur-sm"></div>
        <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal()" @keydown.escape.window="closeModal()">
            <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-5" x-text="modalTitle"></h3>

                <form @submit.prevent="submitStockForm" method="POST" x-ref="stockForm">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                            <x-custom-select
                                name="product_id"
                                placeholder="Pilih produk..."
                                :options="$products->pluck('sku_nama_stok', 'id')"
                                required
                            />
                            <template x-if="errors.product_id">
                                <p class="mt-1 text-xs text-red-600" x-text="errors.product_id.join(', ')"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                            <input type="number" name="qty" min="1" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                   :class="errors.qty ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20 text-red-900' : ''">
                            <template x-if="errors.qty">
                                <p class="mt-1 text-xs text-red-600" x-text="errors.qty.join(', ')"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Referensi <span class="text-gray-400">(opsional)</span></label>
                            <input type="text" name="referensi"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan <span class="text-gray-400">(opsional)</span></label>
                            <textarea name="catatan" rows="2"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="closeModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white rounded-lg transition-colors"
                                :class="tipe === 'in' ? 'bg-green-600 hover:bg-green-700' : (tipe === 'out' ? 'bg-red-600 hover:bg-red-700' : 'bg-yellow-600 hover:bg-yellow-700')"
                                x-text="submitLabel"></button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        {{-- Modal Filter Tanggal --}}
        <div x-show="showFilterModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-white/10 backdrop-blur-sm"></div>
        <div x-show="showFilterModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeFilter()" @keydown.escape.window="closeFilter()">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-5">Filter Tanggal</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" x-model="dateFrom"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" x-model="dateTo"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="closeFilter()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="button" @click="applyFilter()"
                                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Terapkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function stockModal() {
            return {
                showModal: false,
                tipe: 'in',
                modalTitle: '',
                actionUrl: '',
                submitLabel: '',
                search: '',
                dateFrom: '{{ request('date_from') }}',
                dateTo: '{{ request('date_to') }}',
                showFilterModal: false,
                movements: [],
                pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
                errors: {},
                submitted: false,

                async init() {
                    await this.fetchMovements();
                },

                async fetchMovements() {
                    try {
                        const params = new URLSearchParams();
                        if (this.search) params.set('search', this.search);
                        if (this.dateFrom) params.set('date_from', this.dateFrom);
                        if (this.dateTo) params.set('date_to', this.dateTo);

                        const response = await fetch('{{ route('stock.index') }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();
                        this.movements = data.movements;
                        this.pagination = data.pagination;
                    } catch (e) {
                        console.error('Failed to fetch movements:', e);
                    }
                },

                formatDate(dateStr) {
                    const d = new Date(dateStr);
                    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                },

                formatDateOnly(dateStr) {
                    const d = new Date(dateStr);
                    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
                },

                openFilter() {
                    this.showFilterModal = true;
                },

                closeFilter() {
                    this.showFilterModal = false;
                },

                clearFilter() {
                    this.dateFrom = '';
                    this.dateTo = '';
                    this.fetchMovements();
                },

                async applyFilter() {
                    this.closeFilter();
                    await this.fetchMovements();
                },

                get paginationPages() {
                    if (!this.pagination) return [];
                    const pages = [];
                    const start = Math.max(1, this.pagination.current_page - 2);
                    const end = Math.min(this.pagination.last_page, start + 4);
                    for (let i = start; i <= end; i++) pages.push(i);
                    return pages;
                },

                get paginationText() {
                    if (!this.pagination) return '';
                    return `Menampilkan ${this.pagination.from} - ${this.pagination.to} dari ${this.pagination.total} data`;
                },

                async goToPage(page) {
                    if (page < 1 || (this.pagination && page > this.pagination.last_page)) return;
                    const params = new URLSearchParams();
                    if (this.search) params.set('search', this.search);
                    if (this.dateFrom) params.set('date_from', this.dateFrom);
                    if (this.dateTo) params.set('date_to', this.dateTo);
                    params.set('page', page);

                    const response = await fetch('{{ route('stock.index') }}?' + params.toString(), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();
                    this.movements = data.movements;
                    this.pagination = data.pagination;
                },

                openIn() {
                    this.tipe = 'in';
                    this.modalTitle = 'Stok Masuk';
                    this.actionUrl = '{{ route('stock.in') }}';
                    this.submitLabel = 'Masukkan';
                    this.errors = {};
                    this.submitted = false;
                    this.showModal = true;
                    this.$nextTick(() => { if (this.$refs.stockForm) this.$refs.stockForm.reset(); });
                },
                openOut() {
                    this.tipe = 'out';
                    this.modalTitle = 'Stok Keluar';
                    this.actionUrl = '{{ route('stock.out') }}';
                    this.submitLabel = 'Keluarkan';
                    this.errors = {};
                    this.submitted = false;
                    this.showModal = true;
                    this.$nextTick(() => { if (this.$refs.stockForm) this.$refs.stockForm.reset(); });
                },
                closeModal() {
                    this.showModal = false;
                    this.errors = {};
                },

                async submitStockForm(event) {
                    const form = event.target;
                    const formData = new FormData(form);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch(this.actionUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.showToast(data.message || 'Berhasil', 'success');
                            this.closeModal();
                            await this.fetchMovements();
                        } else {
                            this.errors = data.errors || {};
                            this.submitted = true;
                            const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Terjadi kesalahan');
                            this.showToast(msg, 'error');
                        }
                    } catch (e) {
                        console.error('Submit error:', e);
                        this.showToast('Terjadi kesalahan jaringan', 'error');
                    }
                },

                showToast(message, type = 'success') {
                    this.$store.toast.open(message, type);
                },

                get exportUrl() {
                    const params = new URLSearchParams();
                    if (this.dateFrom) params.set('date_from', this.dateFrom);
                    if (this.dateTo) params.set('date_to', this.dateTo);
                    const qs = params.toString();
                    return '{{ route('stock.export') }}' + (qs ? '?' + qs : '');
                }
            }
        }
    </script>
</x-app-layout>

