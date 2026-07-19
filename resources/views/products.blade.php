<x-app-layout>
    <x-slot name="header">Produk</x-slot>

    <div x-data="productModal()" class="space-y-4">
        {{-- Toast --}}
        <template x-teleport="body">
            <div x-show="showToast" x-transition x-cloak
                 class="fixed top-4 right-4 z-50 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl shadow-lg text-sm font-medium">
                <span x-text="toastMessage"></span>
            </div>
        </template>

        {{-- Actions --}}
        <div class="flex justify-between items-center">
            <div class="relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" @input.debounce.300ms="fetchProducts()"
                       placeholder="Cari produk (SKU, Nama)..."
                       class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 w-64">
            </div>
            <div class="flex gap-2">
                <a href="{{ route('products.export', request()->query()) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Excel
                </a>
                <button @click="openCreate()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Produk
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left bg-gray-50">
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">SKU</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Satuan</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Stok</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="product in products" :key="product.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs text-gray-500" x-text="product.sku"></td>
                                <td class="px-5 py-3.5 font-medium text-gray-900" x-text="product.nama"></td>
                                <td class="px-5 py-3.5 text-gray-500" x-text="product.satuan"></td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-semibold transition-all duration-300"
                                          :class="product.stok_saat_ini > 5 ? 'bg-indigo-50 text-indigo-700' : 'bg-red-50 text-red-700'"
                                          x-text="product.stok_saat_ini"></span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <button @click="openEdit(product.id, product.sku, product.nama, product.satuan)"
                                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mr-3">Edit</button>
                                    <button @click="openDelete(product.id, product.nama)"
                                            class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="products.length === 0">
                            <td colspan="5" class="px-5 py-10 text-center text-gray-400">Belum ada produk.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div x-show="pagination && pagination.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
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
        </div>

        {{-- Modal Create/Edit --}}
        <div x-show="showForm" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-white/10 backdrop-blur-sm" @click.self="closeForm()" @keydown.escape="closeForm()">
            <div class="flex items-start justify-center min-h-screen px-4 pt-10 pb-6">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-5" x-text="editId ? 'Edit Produk' : 'Tambah Produk'"></h3>

                <form :action="editId ? '/products/' + editId : '{{ route('products.store') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" :value="editId ? 'PUT' : 'POST'">

                    <div class="space-y-4">
                        <template x-if="editId">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SKU (Otomatis)</label>
                                <input type="text" :value="form.sku" readonly
                                       class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50 text-sm font-mono">
                            </div>
                        </template>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" name="nama" x-model="form.nama" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                            <input type="text" name="satuan" x-model="form.satuan" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="closeForm()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors"
                                x-text="editId ? 'Simpan' : 'Tambah'"></button>
                    </div>
                </form>
            </div>
            </div>
        </div>

        {{-- Modal Delete --}}
        <div x-show="showDelete" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-white/10 backdrop-blur-sm" @click.self="closeDelete()" @keydown.escape="closeDelete()">
            <div class="flex items-start justify-center min-h-screen px-4 pt-10 pb-6">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus Produk</h3>
                <p class="text-sm text-gray-500 mb-6">Yakin ingin menghapus <span class="font-medium text-gray-700" x-text="deleteName"></span>?</p>

                <form :action="'/products/' + deleteId" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-center gap-3">
                        <button type="button" @click="closeDelete()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">Hapus</button>
                    </div>
                </form>
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
        function productModal() {
            return {
                showForm: false,
                showDelete: false,
                showToast: {{ session('success') ? 'true' : 'false' }},
                toastMessage: '{{ session('success') ?? '' }}',
                editId: null,
                deleteId: null,
                deleteName: '',
                search: '',
                products: [],
                pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
                form: { sku: '', nama: '', satuan: '' },
                currentPage: 1,

                async init() {
                    await this.fetchProducts();
                },

                async fetchProducts() {
                    try {
                        const params = new URLSearchParams();
                        if (this.search) params.set('search', this.search);
                        params.set('page', this.currentPage);

                        const response = await fetch('{{ route('products.index') }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();
                        this.products = data.products;
                        this.pagination = data.pagination;
                    } catch (e) {
                        console.error('Failed to fetch products:', e);
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
                    await this.fetchProducts();
                },

                openCreate() {
                    this.editId = null;
                    this.form = { sku: '', nama: '', satuan: '' };
                    this.showForm = true;
                },

                openEdit(id, sku, nama, satuan) {
                    this.editId = id;
                    this.form = { sku, nama, satuan };
                    this.showForm = true;
                },

                closeForm() {
                    this.showForm = false;
                    this.editId = null;
                },

                openDelete(id, nama) {
                    this.deleteId = id;
                    this.deleteName = nama;
                    this.showDelete = true;
                },

                closeDelete() {
                    this.showDelete = false;
                    this.deleteId = null;
                    this.deleteName = '';
                }
            }
        }
    </script>
</x-app-layout>