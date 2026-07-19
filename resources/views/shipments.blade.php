<x-app-layout>
    <x-slot name="header">Pengiriman</x-slot>

    <div x-data="shipmentModal()" class="space-y-4">
        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div class="relative w-full sm:w-auto">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" @input.debounce.300ms="fetchShipments()"
                       placeholder="Cari (No. Shipment, Tujuan, Status, User)..."
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
                <a :href="exportUrl"
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
                    Buat Pengiriman
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
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">No. Shipment</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Tujuan</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Item</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Total Qty</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">User</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="s in shipments" :key="s.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs text-indigo-600 font-medium" x-text="s.no_shipment"></td>
                                <td class="px-5 py-3.5 text-gray-900" x-text="s.tujuan || '-'"></td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                          :class="s.status === 'selesai' ? 'bg-green-50 text-green-700' : (s.status === 'dikirim' ? 'bg-blue-50 text-blue-700' : 'bg-yellow-50 text-yellow-700')"
                                          x-text="s.status.toUpperCase()"></span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-900">
                                    <span x-text="s.items_count + ' item'"></span>
                                    <span x-show="s.items.length" class="text-xs text-gray-400 ml-1" x-text="'(' + [...new Set(s.items.map(i => i.product?.satuan))].filter(Boolean).join(', ') + ')'"></span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-gray-900" x-text="s.total_qty"></td>
                                <td class="px-5 py-3.5 text-gray-500" x-text="s.user?.name || '-'"></td>
                                <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap" x-text="formatDate(s.created_at)"></td>
                                <td class="px-5 py-3.5 text-right">
                                    <template x-if="s.status === 'draft'">
                                        <button @click="openConfirmSent(s.id, s.no_shipment)"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">Kirim</button>
                                    </template>
                                    <template x-if="s.status === 'dikirim'">
                                        <button @click="openConfirmDone(s.id, s.no_shipment)"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium">Selesai</button>
                                    </template>
                                    <template x-if="s.status !== 'draft' && s.status !== 'dikirim'">
                                        <span class="text-gray-400 text-sm">-</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="shipments.length === 0">
                            <td colspan="8" class="px-5 py-10 text-center text-gray-400">Belum ada pengiriman.</td>
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
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-5">Buat Pengiriman</h3>

                <form action="{{ route('shipment.store') }}" method="POST">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Shipment</label>
                            <input type="text" name="no_shipment" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan</label>
                            <input type="text" name="tujuan"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Resi <span class="text-gray-400">(opsional)</span></label>
                            <input type="text" name="no_resi"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan <span class="text-gray-400">(opsional)</span></label>
                            <textarea name="catatan" rows="2"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>

                        {{-- Items --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Item</label>
                                <button type="button" @click="addItem()"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">+ Tambah item</button>
                            </div>

                            <template x-for="(item, i) in items" :key="i">
                                <div class="flex gap-2 mb-2">
                                    <div class="flex-1 relative">
                                        <input type="hidden" :name="'items['+i+'][product_id]'" x-model="item.product_id">
                                        <button type="button" @click="toggleSelect(i)"
                                                class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-150 hover:border-gray-400"
                                                :class="item.product_id ? 'text-gray-900' : 'text-gray-400'">
                                            <span class="truncate" x-text="item.product_id ? getProductLabel(item.product_id) : 'Pilih produk...'"></span>
                                            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': openIndex === i }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div x-show="openIndex === i" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                                             @click.outside="openIndex = -1" @keydown.escape="openIndex = -1"
                                             class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-xl shadow-lg shadow-gray-200/50 overflow-hidden">
                                            <div x-show="productOptions.length > 8" class="p-2 border-b border-gray-100">
                                                <div class="relative">
                                                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                    </svg>
                                                    <input type="text" x-model="item.q" placeholder="Cari produk..."
                                                           class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition-all">
                                                </div>
                                            </div>
                                            <div class="max-h-56 overflow-y-auto overscroll-contain">
                                                <template x-for="(opt, j) in filteredItemOptions(i)" :key="j">
                                                    <button type="button" @click="item.product_id = opt.value; item.q = ''; openIndex = -1"
                                                            class="w-full text-left px-3 py-2.5 text-sm transition-all duration-100 flex items-center justify-between border-b border-gray-50 last:border-b-0"
                                                            :class="opt.value === item.product_id ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-indigo-50/50 hover:text-gray-900'">
                                                        <span class="truncate" x-text="opt.label"></span>
                                                        <svg x-show="opt.value === item.product_id" class="w-4 h-4 text-indigo-500 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </template>
                                                <div x-show="filteredItemOptions(i).length === 0"
                                                     class="px-3 py-8 text-sm text-gray-400 text-center">
                                                    <svg class="w-6 h-6 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Produk tidak ditemukan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="number" :name="'items['+i+'][qty]'" min="1" placeholder="Qty" required
                                           class="w-24 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <button type="button" @click="removeItem(i)" x-show="items.length > 1"
                                            class="text-red-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                        <button type="button" @click="closeModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Simpan (Draft)</button>
                    </div>
                </form>
            </div>
            </div>
        </div>

        {{-- Modal Confirm Send (Kirim) --}}
        <div x-show="showConfirmSent" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-white/10 backdrop-blur-sm"></div>
        <div x-show="showConfirmSent" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeConfirmSent()" @keydown.escape.window="closeConfirmSent()">
            <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-1m0 0l2 1m-2-1v10a1 1 0 002 1h12a1 1 0 002-1v-9a1 1 0 00-1-1h-5m-8 4h12"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi Kirim</h3>
                <p class="text-sm text-gray-500 mb-2">Yakin ingin mengirim shipment</p>
                <p class="text-sm font-semibold text-indigo-600 mb-6" x-text="confirmSentNo"></p>
                <p class="text-xs text-gray-400 mb-6 -mt-4">Stok akan dikurangi secara otomatis.</p>

                <form method="POST" :action="'/shipment/' + confirmSentId + '/mark-sent'">
                    @csrf
                    <div class="flex justify-center gap-3">
                        <button type="button" @click="closeConfirmSent()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Kirim</button>
                    </div>
                </form>
            </div>
            </div>
        </div>

        {{-- Modal Confirm Done (Selesai) --}}
        <div x-show="showConfirmDone" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-white/10 backdrop-blur-sm"></div>
        <div x-show="showConfirmDone" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeConfirmDone()" @keydown.escape.window="closeConfirmDone()">
            <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi Selesai</h3>
                <p class="text-sm text-gray-500 mb-2">Yakin ingin menyelesaikan shipment</p>
                <p class="text-sm font-semibold text-indigo-600 mb-6" x-text="confirmDoneNo"></p>

                <form method="POST" :action="'/shipment/' + confirmDoneId + '/mark-done'">
                    @csrf
                    <div class="flex justify-center gap-3">
                        <button type="button" @click="closeConfirmDone()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">Selesai</button>
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

    <script>
        function shipmentModal() {
            return {
                showModal: false,
                items: [{ product_id: '', qty: '', q: '' }],
                openIndex: -1,
                productOptions: [
                    @foreach ($products as $p)
                        { value: '{{ $p->id }}', label: '{{ $p->sku }} - {{ $p->nama }} ({{ $p->satuan }})' },
                    @endforeach
                ],
                // Confirm Sent
                showConfirmSent: false,
                confirmSentId: null,
                confirmSentNo: '',
                // Confirm Done
                showConfirmDone: false,
                confirmDoneId: null,
                confirmDoneNo: '',
                // AJAX search
                search: '',
                shipments: [],
                pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
                currentPage: 1,
                dateFrom: '',
                dateTo: '',
                showFilterModal: false,

                async init() {
                    await this.fetchShipments();
                    @if (session('success'))
                        this.$store.toast.open('{{ session('success') }}');
                    @endif
                    setInterval(() => this.fetchShipments(), 30000);
                },

                async fetchShipments() {
                    try {
                        const params = new URLSearchParams();
                        if (this.search) params.set('search', this.search);
                        if (this.dateFrom) params.set('date_from', this.dateFrom);
                        if (this.dateTo) params.set('date_to', this.dateTo);
                        params.set('page', this.currentPage);

                        const response = await fetch('{{ route('shipments.index') }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();
                        this.shipments = data.shipments;
                        this.pagination = data.pagination;
                    } catch (e) {
                        console.error('Failed to fetch shipments:', e);
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
                    this.fetchShipments();
                },

                async applyFilter() {
                    this.closeFilter();
                    await this.fetchShipments();
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
                    await this.fetchShipments();
                },

                getProductLabel(id) {
                    if (!id) return 'Pilih produk...';
                    const found = this.productOptions.find(o => String(o.value) === String(id));
                    return found ? found.label : 'Pilih produk...';
                },
                filteredItemOptions(i) {
                    const item = this.items[i];
                    if (!item || !item.q) return this.productOptions;
                    const q = item.q.toLowerCase();
                    return this.productOptions.filter(o => o.label.toLowerCase().includes(q));
                },
                toggleSelect(i) {
                    this.openIndex = this.openIndex === i ? -1 : i;
                    if (this.openIndex === i) this.items[i].q = '';
                },
                openCreate() {
                    this.items = [{ product_id: '', qty: '', q: '' }];
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                    this.openIndex = -1;
                },
                addItem() {
                    this.items.push({ product_id: '', qty: '', q: '' });
                },
                removeItem(i) {
                    this.items.splice(i, 1);
                },
                openConfirmSent(id, no) {
                    this.confirmSentId = id;
                    this.confirmSentNo = no;
                    this.showConfirmSent = true;
                },
                closeConfirmSent() {
                    this.showConfirmSent = false;
                    this.confirmSentId = null;
                    this.confirmSentNo = '';
                },
                openConfirmDone(id, no) {
                    this.confirmDoneId = id;
                    this.confirmDoneNo = no;
                    this.showConfirmDone = true;
                },
                closeConfirmDone() {
                    this.showConfirmDone = false;
                    this.confirmDoneId = null;
                    this.confirmDoneNo = '';
                },
                get exportUrl() {
                    const params = new URLSearchParams();
                    if (this.dateFrom) params.set('date_from', this.dateFrom);
                    if (this.dateTo) params.set('date_to', this.dateTo);
                    const qs = params.toString();
                    return '{{ route('shipments.export') }}' + (qs ? '?' + qs : '');
                }
            }
        }
    </script>
</x-app-layout>

