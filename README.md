# SimpleStockApp

Aplikasi manajemen inventaris/stock gudang berbasis **Laravel 13** + **Alpine.js** + **Tailwind CSS**. Dirancang untuk memudahkan pencatatan barang masuk, keluar, pengiriman, dan retur dengan update stok mendekati real-time.

## Fitur

### Dashboard
- Ringkasan jumlah produk, total stok, produk stok rendah, pengiriman aktif, dan retur pending
- Update stok otomatis setiap 5 detik via AJAX polling
- Tabel produk dengan pencarian dan paginasi

### Manajemen Produk
- CRUD produk (SKU otomatis: `BRG-XXXXXX`)
- Pencarian produk

### Mutasi Stok
- **Stock In** — catat barang masuk, stok otomatis bertambah
- **Stock Out** — catat barang keluar, stok otomatis berkurang
- Riwayat mutasi dengan filter tanggal dan pencarian

### Pengiriman
- Buat pengiriman dengan beberapa item barang
- Alur status: **Draft** → **Dikirim** (stok otomatis berkurang) → **Selesai**
- Nomor resi dan tujuan pengiriman

### Retur Barang
- Ajukan retur (dari pengiriman atau mandiri)
- Alur status: **Pending** → **Disetujui** (stok otomatis kembali) / **Ditolak**

### Manajemen Pengguna (Admin)
- CRUD pengguna dengan role: **Admin** atau **User**
- Hanya admin yang bisa mengakses halaman ini

### Export Excel
- Export data ke XLSX untuk: Produk, Mutasi Stok, Pengiriman, Retur, Pengguna

### Real-time (Opsional)
- Event `StockUpdated` via WebSocket (Laravel Reverb / Pusher) — pantau perubahan stok langsung

## Tech Stack

| Komponen | Teknologi |
|---|---|
| **Backend** | Laravel 13.x (PHP 8.3+) |
| **Frontend** | Blade + Alpine.js 3 + Tailwind CSS 3 |
| **Auth** | Laravel Breeze (session-based) |
| **Database** | MySQL / SQLite / PostgreSQL |
| **Export** | maatwebsite/Laravel-Excel |
| **Broadcast** | Laravel Reverb / Pusher (opsional) |
| **Build** | Vite + vite-plugin-pwa |
| **PWA** | Siap dijadikan Progressive Web App |

## Persyaratan Sistem

- PHP 8.3+
- Composer 2.x
- Node.js 18+ (untuk frontend build)
- Database (MySQL, SQLite, atau PostgreSQL)
- Web server (Apache/Nginx) atau shared hosting

## Instalasi Lokal

```bash
# Clone repositori
git clone https://github.com/yasiryas/SimpleStockApp.git
cd SimpleStockApp

# Install dependensi PHP
composer install

# Install dependensi frontend
npm install

# Konfigurasi environment
cp .env.example .env
php artisan key:generate

# Setup database (SQLite default)
# Atau atur DB_DATABASE di .env untuk MySQL/PostgreSQL

# Jalankan migrasi
php artisan migrate

# Build frontend
npm run build

# Jalankan development server
php artisan serve
```

Akses di `http://localhost:8000`. Daftarkan akun pertama sebagai **admin**.

## Deploy ke cPanel / Shared Hosting

1. Upload seluruh file (kecuali folder `node_modules`) ke hosting
2. Set `public/` sebagai document root
3. Setup environment variables di `.env` (sesuaikan dengan database hosting)
4. Jalankan `php artisan migrate` via terminal hosting
5. Build frontend: jalankan `npm run build` lalu upload folder `public/build/`
6. (Opsional) Setup cron job untuk queue worker

## Struktur Menu

| Menu | Akses |
|---|---|
| Dashboard | Semua user |
| Produk | Semua user |
| Mutasi Stok | Semua user |
| Pengiriman | Semua user |
| Retur | Semua user |
| Manajemen Pengguna | **Admin** saja |

## Screenshots
<img width="469" height="412" alt="image" src="https://github.com/user-attachments/assets/c275c6f8-4920-4139-a08a-a72ea9ccdd15" />
<img width="478" height="275" alt="image" src="https://github.com/user-attachments/assets/389c3318-76ea-41f5-8f17-67a6c924178c" />



## Lisensi

MIT — lihat [LICENSE](LICENSE).
