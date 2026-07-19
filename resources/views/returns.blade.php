<x-app-layout>
    <x-slot name="header">Retur</x-slot>

    <div x-data="returnModal()" class="space-y-4">
        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div class="relative w-full sm:w-auto">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" @input.debounce.300ms="fetchReturns()"
                       placeholder="Cari (Produk, Alasan, Status, Shipment, User)..."
                       class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 w-64">
            </div>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <button @click="openFilter" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 18a1 1 0 01-1-1V6a1 1 0 011-1h12a1 1 0 011 1v11a1 1 0 01-1 1H5z"/>
                    </svg>
                    Filter Tanggal
                </button>
                <a href="{{ route('returns.export', request()->query()) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Excel
                </a>
                <button @click="openCreate()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 text-white text-sm font-semibold rounded-lg hover:bg-yellow-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Ajukan Retur
                </button>
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
                <button @click="clearFilter" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Hapus</button>
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
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Qty</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Shipment</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Alasan</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">User</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="r in returns" :key="r.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap" x-text="formatDate(r.created_at)"></td>
                                <td class="px-5 py-3.5 font-medium text-gray-900" x-text="r.product?.nama || '-'"></td>
                                <td class="px-5 py-3.5 font-mono text-gray-900" x-text="r.qty"></td>
                                <td class="px-5 py-3.5 font-mono text-xs text-gray-500" x-text="r.shipment?.no_shipment || '-'"></td>
                                <td class="px-5 py-3.5 text-gray-500 max-w-[200px] truncate" x-text="r.alasan"></td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                          :class="r.status === 'disetujui' ? 'bg-green-50 text-green-700' : (r.status === 'ditolak' ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700')"
                                          x-text="r.status.toUpperCase()"></span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500" x-text="r.user?.name || '-'"></td>
                                <td class="px-5 py-3.5 text-right">
                                    <template x-if="r.status === 'pending'">
                                        <button @click="openConfirmApprove(r.id, r.product?.nama || '-')"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium mr-2">Setujui</button>
                                        <button @click="openConfirmReject(r.id, r.product?.nama || '-')"
                                                class="text-red-500 hover:text-red-700 text-sm font-medium">Tolak</button>
                                    </template>
                                    <template x-if="r.status !== 'pending'">
                                        <span class="text-gray-400 text-sm">-</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="returns.length === 0">
                            <td colspan="8" class="px-5 py-10 text-center text-gray-400">Belum ada retur.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <template x-if="pagination && pagination.last_page > 1">
                <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
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
        </div>

        {{-- Modal Create --}}
        <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-white/10 backdrop-blur-sm"></div>
        <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal()" @keydown.escape.window="closeModal()">
            <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-5">Ajukan Retur</h3>

                <form action="{{ route('return.store') }}" method="POST">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                            <x-custom-select
                                name="product_id"
                                placeholder="Pilih produk..."
                                :options="$products->pluck('sku_nama', 'id')"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                            <input type="number" name="qty" min="1" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Terkait Shipment <span class="text-gray-400">(opsional)</span></label>
                            <x-custom-select
                                name="shipment_id"
                                placeholder="Retur umum (tanpa shipment)"
                                :options="$shipments->mapWithKeys(fn($s) => [$s->id => $s->no_shipment . ' - ' . ($s->tujuan ?? '-')])"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                            <textarea name="alasan" rows="2" required
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="closeModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 transition-colors">Ajukan</button>
                    </div>
                </form>
            </div>
            </div>
        </div>

        {{-- Modal Confirm Approve --}}
        <div x-show="showConfirmApprove" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-white/10 backdrop-blur-sm"></div>
        <div x-show="showConfirmApprove" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeConfirmApprove()" @keydown.escape.window="closeConfirmApprove()">
            <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Setujui Retur</h3>
                <p class="text-sm text-gray-500 mb-2">Yakin ingin menyetujui retur</p>
                <p class="text-sm font-semibold text-gray-900 mb-6" x-text="confirmApproveProduct"></p>
                <p class="text-xs text-gray-400 mb-6 -mt-4">Stok akan ditambahkan kembali secara otomatis.</p>

                <form method="POST" :action="'/return/' + confirmApproveId + '/approve'">
                    @csrf
                    <div class="flex justify-center gap-3">
                        <button type="button" @click="closeConfirmApprove()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">Setujui</button>
                    </div>
                </form>
            </div>
            </div>
        </div>

        {{-- Modal Confirm Reject --}}
        <div x-show="showConfirmReject" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-white/10 backdrop-blur-sm"></div>
        <div x-show="showConfirmReject" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeConfirmReject()" @keydown.escape.window="closeConfirmReject()">
            <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Tolak Retur</h3>
                <p class="text-sm text-gray-500 mb-2">Yakin ingin menolak retur</p>
                <p class="text-sm font-semibold text-gray-900 mb-6" x-text="confirmRejectProduct"></p>

                <form method="POST" :action="'/return/' + confirmRejectId + '/reject'">
                    @csrf
                    <div class="flex justify-center gap-3">
                        <button type="button" @click="closeConfirmReject()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">Tolak</button>
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

        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Alpine.store('toast', { show: true, message: '{{ session('success') }}' });
                });
            </script>
        @endif
    </div>

    <script>
        function returnModal() {
            return {
                showModal: false,
                // Confirm Approve
                showConfirmApprove: false,
                confirmApproveId: null,
                confirmApproveProduct: '',
                // Confirm Reject
                showConfirmReject: false,
                confirmRejectId: null,
                confirmRejectProduct: '',
                // AJAX search
                search: '',
                returns: [],
                pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
                currentPage: 1,
                dateFrom: '',
                dateTo: '',
                showFilterModal: false,

                async init() {
                    await this.fetchReturns();
                    @if (session('success'))
                        this.$store.toast.open('{{ session('success') }}');
                    @endif
                    setInterval(() => this.fetchReturns(), 30000);
                },

                async fetchReturns() {
                    try {
                        const params = new URLSearchParams();
                        if (this.search) params.set('search', this.search);
                        if (this.dateFrom) params.set('date_from', this.dateFrom);
                        if (this.dateTo) params.set('date_to', this.dateTo);
                        params.set('page', this.currentPage);

                        const response = await fetch('{{ route('returns.index') }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();
                        this.returns = data.returns;
                        this.pagination = data.pagination;
                    } catch (e) {
                        console.error('Failed to fetch returns:', e);
                    }
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
                    this.currentPage = page;
                    await this.fetchReturns();
                },

                formatDate(dateStr) {
                    const d = new Date(dateStr);
                    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }).replace(',', '');
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
                    this.fetchReturns();
                },

                async applyFilter() {
                    this.closeFilter();
                    await this.fetchReturns();
                },

                openCreate() {
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                },
                openConfirmApprove(id, product) {
                    this.confirmApproveId = id;
                    this.confirmApproveProduct = product;
                    this.showConfirmApprove = true;
                },
                closeConfirmApprove() {
                    this.showConfirmApprove = false;
                    this.confirmApproveId = null;
                    this.confirmApproveProduct = '';
                },
                openConfirmReject(id, product) {
                    this.confirmRejectId = id;
                    this.confirmRejectProduct = product;
                    this.showConfirmReject = true;
                },
                closeConfirmReject() {
                    this.showConfirmReject = false;
                    this.confirmRejectId = null;
                    this.confirmRejectProduct = '';
                }
            }
        }
    </script>
</x-app-layout>

