# PRD — Aplikasi Olah Stok Gudang (Realtime)

## 1. Ringkasan
Aplikasi berbasis web untuk mengelola stok gudang secara sederhana, dengan update realtime menggunakan **Laravel Reverb** (WebSocket resmi Laravel). Ditujukan untuk tim gudang kecil-menengah yang butuh visibilitas stok yang akurat tanpa harus refresh manual.

## 2. Tujuan
- Mencatat mutasi stok (masuk/keluar) secara akurat dan mudah dilacak.
- Menampilkan total stok yang selalu update secara realtime ke semua user yang sedang membuka dashboard.
- Mendukung proses pengiriman dan retur barang dengan histori yang jelas.

## 3. Tech Stack
| Komponen | Pilihan |
|---|---|
| Backend | Laravel 11+ |
| Realtime | Laravel Reverb + Laravel Echo |
| Frontend | Blade + Alpine.js + Tailwind CSS |
| Database | MySQL |
| Auth | Laravel Breeze (sederhana) |
| Deployment | VPS (wajib, karena Reverb butuh proses persisten) |

## 4. Fitur

### 4.1 Login (sederhana)
- Login dengan email & password (Laravel Breeze, tanpa fitur ekstra seperti 2FA/social login).
- Satu role: **admin gudang** (tidak ada multi-role di versi awal, bisa ditambah nanti).
- Halaman: Login, Logout. Tidak ada registrasi publik — akun dibuat manual via seeder/admin.

### 4.2 Stock In
- Form input: produk, jumlah, sumber (supplier/PO), catatan, tanggal.
- Setelah simpan → update `stok_saat_ini` pada produk + catat ke `stock_movements` (tipe: `in`).
- Broadcast event `StockUpdated` ke semua client yang terhubung.

### 4.3 Stock Out
- Form input: produk, jumlah, tujuan/keperluan, catatan.
- Validasi: jumlah keluar tidak boleh melebihi stok tersedia.
- Update stok + catat mutasi (tipe: `out`) + broadcast realtime.

### 4.4 Total Stock (Dashboard)
- Tabel/list semua produk dengan kolom: SKU, nama, stok saat ini, satuan.
- Angka stok **update otomatis realtime** via Reverb tanpa reload halaman.
- Filter pencarian produk sederhana (by nama/SKU).

### 4.5 Pengiriman (Shipment)
- Buat pengiriman: pilih produk + qty (bisa multi-item per pengiriman), tujuan, no. resi (opsional), status (draft → dikirim → selesai).
- Saat status jadi "dikirim" → otomatis mengurangi stok (tercatat sebagai `stock_movements` tipe `out`, referensi ke shipment).
- List riwayat pengiriman dengan status.

### 4.6 Return (Retur)
- Input retur: terkait pengiriman tertentu (opsional) atau retur umum, produk, qty, alasan.
- Saat retur disetujui → stok bertambah kembali (`stock_movements` tipe `return`).
- List riwayat retur dengan status (pending/disetujui/ditolak).

## 5. Struktur Data (ringkas)
- **users** — id, name, email, password
- **products** — id, sku, nama, satuan, stok_saat_ini
- **stock_movements** — id, product_id, tipe (in/out/return), qty, referensi, user_id, catatan, created_at
- **shipments** — id, no_resi, tujuan, status, created_by
- **shipment_items** — shipment_id, product_id, qty
- **returns** — id, shipment_id (nullable), product_id, qty, alasan, status

## 6. Alur Realtime (Reverb)
1. Aksi user (stock in/out, pengiriman, retur) → controller update DB.
2. Controller dispatch event `StockUpdated` (implements `ShouldBroadcast`).
3. Reverb server broadcast event ke channel (misal `stok-gudang`).
4. Laravel Echo di frontend listen channel tsb → update angka di UI tanpa reload.

## 7. Design System (Simple)
- **Warna:** 1 warna primer ungu (`#7C3AED`), netral abu-abu untuk teks/border, hijau untuk status positif (stok in/selesai), merah untuk stok out/rendah, kuning untuk pending. Ungu dipilih karena beda dari biru standar ERP tapi tetap terasa profesional & fokus (banyak dipakai sistem enterprise modern seperti Odoo).
- **Tipografi:** 1 font sans-serif (Inter/Figtree), 2-3 ukuran saja (heading, body, small).
- **Komponen:** card sederhana untuk ringkasan stok, tabel dengan zebra-striping tipis, badge status berwarna, tombol solid untuk aksi utama & outline untuk aksi sekunder.
- **Layout:** sidebar kiri sederhana (Dashboard, Stock In, Stock Out, Pengiriman, Retur), konten utama tanpa dekorasi berlebih — fokus ke keterbacaan angka stok.

## 8. Di Luar Cakupan (v1)
- Multi-gudang/lokasi
- Multi-role & permission granular
- Laporan/export PDF/Excel
- Notifikasi WhatsApp/email

## 9. Kebutuhan Deploy
- **VPS** (bukan shared hosting) — Reverb butuh proses berjalan terus (`php artisan reverb:start` via supervisor).

## 10. Prinsip "Ringan"
Supaya aplikasi tetap ringan (di server maupun saat development):
- Tidak pakai frontend framework berat (Vue/React) — cukup Blade + Alpine.js.
- Tailwind CSS dipakai lewat build minimal (Vite bawaan Laravel), bukan CDN, supaya file CSS final tetap kecil (purge otomatis class yang tidak dipakai).
- Reverb dijalankan sebagai satu proses tunggal via Supervisor — tidak butuh Redis/queue worker tambahan di awal (opsional untuk versi berikutnya kalau load makin besar).
- Query database dijaga sederhana: `stok_saat_ini` disimpan sebagai kolom cache di tabel `products` (bukan dihitung ulang dari SUM semua `stock_movements` setiap request) supaya dashboard tetap cepat walau data mutasi sudah banyak.
