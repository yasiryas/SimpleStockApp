<x-app-layout>
    <x-slot name="header">Mutasi Stok</x-slot>

    <div x-data="stockModal()" class="space-y-4">
        {{-- Actions --}}
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
                        @forelse ($movements as $m)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap">{{ $m->created_at->format('d M H:i') }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">{{ $m->product->nama ?? '-' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $m->tipe === 'in' ? 'bg-green-50 text-green-700' : ($m->tipe === 'out' ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700') }}">
                                        {{ $m->tipe === 'in' ? 'MASUK' : ($m->tipe === 'out' ? 'KELUAR' : 'RETUR') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-gray-900">{{ $m->qty }}</td>
                                <td class="px-5 py-3.5 text-gray-500">{{ $m->referensi ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-500">{{ $m->user->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-400">Belum ada mutasi stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $movements->links() }}
        </div>

        {{-- Modal Stok In / Out / Return --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-black/40" @click.self="closeModal()">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-5" x-text="modalTitle"></h3>

                <form :action="actionUrl" method="POST">
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

    <script>
        function stockModal() {
            return {
                showModal: false,
                tipe: 'in',
                modalTitle: '',
                actionUrl: '',
                submitLabel: '',
                openIn() {
                    this.tipe = 'in';
                    this.modalTitle = 'Stok Masuk';
                    this.actionUrl = '{{ route('stock.in') }}';
                    this.submitLabel = 'Masukkan';
                    this.showModal = true;
                },
                openOut() {
                    this.tipe = 'out';
                    this.modalTitle = 'Stok Keluar';
                    this.actionUrl = '{{ route('stock.out') }}';
                    this.submitLabel = 'Keluarkan';
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                }
            }
        }
    </script>
</x-app-layout>
