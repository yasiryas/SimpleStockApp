# Stok Gudang

Aplikasi olah stok gudang berbasis Laravel dengan update stok mendekati realtime menggunakan AJAX polling — tanpa websocket, ringan, dan mudah di-deploy di VPS maupun shared hosting.

## Fitur

- Login sederhana (Laravel Breeze)
- Stock In — pencatatan barang masuk
- Stock Out — pencatatan barang keluar
- Total Stock — dashboard dengan angka stok yang update otomatis
- Pengiriman — pencatatan pengiriman barang keluar
- Return — pencatatan retur barang

## Tech stack

| Komponen | Pilihan |
|---|---|
| Backend | Laravel 11+ |
| Update stok | AJAX pull (polling) via Alpine.js |
| Frontend | Blade + Alpine.js + Tailwind CSS |
| Database | MySQL |
| Auth | Laravel Breeze |

## Dokumentasi

- [PRD lengkap](docs/PRD.md)
- [Panduan vibecode dengan OpenCode](docs/PANDUAN-OPENCODE.md)

## Instalasi lokal

```bash
git clone <url-repo-ini>
cd stok-gudang
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run dev
php artisan serve
```

## Lisensi

MIT — lihat [LICENSE](LICENSE)
