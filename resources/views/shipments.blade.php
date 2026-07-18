<x-app-layout>
    <x-slot name="header">Pengiriman</x-slot>

    <div x-data="shipmentModal()" class="space-y-4">
        {{-- Actions --}}
        <div class="flex justify-end">
            <button @click="openCreate()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Pengiriman
            </button>
        </div>

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
                        @forelse ($shipments as $shipment)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs text-indigo-600 font-medium">{{ $shipment->no_shipment }}</td>
                                <td class="px-5 py-3.5 text-gray-900">{{ $shipment->tujuan ?? '-' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $shipment->status === 'selesai' ? 'bg-green-50 text-green-700' : ($shipment->status === 'dikirim' ? 'bg-blue-50 text-blue-700' : 'bg-yellow-50 text-yellow-700') }}">
                                        {{ strtoupper($shipment->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-900">{{ $shipment->items->count() }} produk</td>
                                <td class="px-5 py-3.5 font-mono text-gray-900">{{ $shipment->items->sum('qty') }}</td>
                                <td class="px-5 py-3.5 text-gray-500">{{ $shipment->user->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap">{{ $shipment->created_at->format('d M Y H:i') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    @if ($shipment->status === 'draft')
                                        <button @click="openConfirmSent({{ $shipment->id }}, '{{ $shipment->no_shipment }}')"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">Kirim</button>
                                    @elseif ($shipment->status === 'dikirim')
                                        <button @click="openConfirmDone({{ $shipment->id }}, '{{ $shipment->no_shipment }}')"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium">Selesai</button>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-gray-400">Belum ada pengiriman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Create --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-black/40" @click.self="closeModal()">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto">
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
                                                class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-150"
                                                :class="item.product_id ? 'text-gray-900' : 'text-gray-400'">
                                            <span class="truncate" x-text="item.product_id ? getProductLabel(item.product_id) : 'Pilih produk...'"></span>
                                            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': openIndex === i }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div x-show="openIndex === i" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                             @click.outside="openIndex = -1"
                                             class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                                            <div class="max-h-56 overflow-y-auto">
                                                <template x-for="(opt, j) in productOptions" :key="j">
                                                    <button type="button" @click="item.product_id = opt.value; openIndex = -1"
                                                            class="w-full text-left px-3 py-2.5 text-sm transition-colors duration-100 flex items-center justify-between"
                                                            :class="opt.value === item.product_id ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50'">
                                                        <span x-text="opt.label"></span>
                                                        <svg x-show="opt.value === item.product_id" class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </template>
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

        {{-- Modal Confirm Send (Kirim) --}}
        <div x-show="showConfirmSent" x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-black/40" @click.self="closeConfirmSent()">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center">
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

        {{-- Modal Confirm Done (Selesai) --}}
        <div x-show="showConfirmDone" x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-black/40" @click.self="closeConfirmDone()">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center">
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

    <script>
        function shipmentModal() {
            return {
                showModal: false,
                items: [{ product_id: '', qty: '' }],
                openIndex: -1,
                productOptions: [
                    @foreach ($products as $p)
                        { value: '{{ $p->id }}', label: '{{ $p->sku }} - {{ $p->nama }}' },
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
                getProductLabel(id) {
                    if (!id) return 'Pilih produk...';
                    const found = this.productOptions.find(o => String(o.value) === String(id));
                    return found ? found.label : 'Pilih produk...';
                },
                toggleSelect(i) {
                    this.openIndex = this.openIndex === i ? -1 : i;
                },
                openCreate() {
                    this.items = [{ product_id: '', qty: '' }];
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                    this.openIndex = -1;
                },
                addItem() {
                    this.items.push({ product_id: '', qty: '' });
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
                }
            }
        }
    </script>
</x-app-layout>
