<x-app-layout>
    <x-slot name="header">Retur</x-slot>

    <div x-data="returnModal()" class="space-y-4">
        {{-- Toast --}}
        <template x-teleport="body">
            <div x-show="showToast" x-transition x-cloak
                 class="fixed top-4 right-4 z-50 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl shadow-lg text-sm font-medium">
                <span x-text="toastMessage"></span>
            </div>
        </template>

        {{-- Actions --}}
        <div class="flex justify-end">
            <button @click="openCreate()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 text-white text-sm font-semibold rounded-lg hover:bg-yellow-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Ajukan Retur
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
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Qty</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Shipment</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Alasan</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wider">User</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($returns as $return)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap">{{ $return->created_at->format('d M H:i') }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">{{ $return->product->nama ?? '-' }}</td>
                                <td class="px-5 py-3.5 font-mono text-gray-900">{{ $return->qty }}</td>
                                <td class="px-5 py-3.5 font-mono text-xs text-gray-500">{{ $return->shipment->no_shipment ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-500 max-w-[200px] truncate">{{ $return->alasan }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $return->status === 'disetujui' ? 'bg-green-50 text-green-700' : ($return->status === 'ditolak' ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700') }}">
                                        {{ strtoupper($return->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500">{{ $return->user->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    @if ($return->status === 'pending')
                                        <button @click="openConfirmApprove({{ $return->id }}, '{{ $return->product->nama ?? '-' }}')"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium mr-2">Setujui</button>
                                        <button @click="openConfirmReject({{ $return->id }}, '{{ $return->product->nama ?? '-' }}')"
                                                class="text-red-500 hover:text-red-700 text-sm font-medium">Tolak</button>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-gray-400">Belum ada retur.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Create --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-black/40" @click.self="closeModal()">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
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

        {{-- Modal Confirm Approve --}}
        <div x-show="showConfirmApprove" x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-black/40" @click.self="closeConfirmApprove()">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center">
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

        {{-- Modal Confirm Reject --}}
        <div x-show="showConfirmReject" x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-black/40" @click.self="closeConfirmReject()">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center">
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
                showToast: {{ session('success') ? 'true' : 'false' }},
                toastMessage: '{{ session('success') ?? '' }}',
                // Confirm Approve
                showConfirmApprove: false,
                confirmApproveId: null,
                confirmApproveProduct: '',
                // Confirm Reject
                showConfirmReject: false,
                confirmRejectId: null,
                confirmRejectProduct: '',
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
