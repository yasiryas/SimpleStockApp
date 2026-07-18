<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'SimpleStock') }} — Kelola Stok dengan Mudah</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ scrolled: false }" x-on:scroll.window="scrolled = window.scrollY > 20" class="min-h-screen bg-white">

            {{-- Navbar --}}
            <nav x-bind:class="scrolled ? 'bg-white/90 backdrop-blur-lg shadow-sm border-b border-gray-100' : 'bg-transparent'"
                 class="fixed top-0 inset-x-0 z-50 transition-all duration-300 ease-out">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16 lg:h-20">
                        <a href="/" class="flex items-center gap-2.5 group">
                            <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center shadow-md shadow-indigo-200 group-hover:shadow-lg group-hover:shadow-indigo-300 transition-shadow">
                                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                </svg>
                            </div>
                            <span class="font-extrabold text-xl text-gray-900 tracking-tight">{{ config('app.name') }}</span>
                        </a>

                        @if (Route::has('login'))
                            <div class="flex items-center gap-2 sm:gap-3">
                                @auth
                                    <a href="{{ url('/dashboard') }}"
                                       class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="text-sm font-medium text-gray-600 hover:text-gray-900 px-3 py-2 transition-colors">
                                        Masuk
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}"
                                           class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all">
                                            Daftar Gratis
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </nav>

            {{-- Hero Section --}}
            <section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-indigo-50/30">
                {{-- Decorative background elements --}}
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-indigo-100/40 blur-3xl"></div>
                    <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-indigo-100/30 blur-3xl"></div>
                    <div class="absolute top-1/3 left-1/4 w-4 h-4 rounded-full bg-indigo-300/40 animate-pulse"></div>
                    <div class="absolute top-1/4 right-1/3 w-6 h-6 rounded-full bg-indigo-200/50 animate-pulse" style="animation-delay: 1s"></div>
                    <div class="absolute bottom-1/3 right-1/4 w-3 h-3 rounded-full bg-indigo-300/40 animate-pulse" style="animation-delay: 2s"></div>
                </div>

                <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 lg:py-40">
                    <div class="max-w-3xl mx-auto text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-100/80 border border-indigo-200/50 text-indigo-700 text-xs font-semibold tracking-wide mb-8">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                            Solusi manajemen stok #1 untuk UKM
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-[1.1] tracking-tight">
                            Kelola Stok Barang<br/>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">dengan Mudah & Cepat</span>
                        </h1>

                        <p class="mt-6 text-lg sm:text-xl text-gray-500 leading-relaxed max-w-2xl mx-auto">
                            Catat mutasi stok, buat pengiriman, dan pantau ketersediaan barang secara realtime.
                            Semua dalam satu platform yang sederhana dan powerful.
                        </p>

                        @if (Route::has('login') && !Auth::check())
                            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                                <a href="{{ route('register') }}"
                                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-xl shadow-indigo-200 hover:shadow-indigo-300 transition-all text-base">
                                    Mulai Sekarang
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                                <a href="{{ route('login') }}"
                                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all text-base">
                                    Sudah punya akun? Masuk
                                </a>
                            </div>
                        @endif

                        {{-- Stats row --}}
                        <div class="mt-16 grid grid-cols-2 sm:grid-cols-4 gap-6 sm:gap-8 max-w-2xl mx-auto">
                            <div class="text-center">
                                <div class="text-2xl sm:text-3xl font-extrabold text-gray-900">10K+</div>
                                <div class="text-xs sm:text-sm text-gray-500 mt-1">Pengguna Aktif</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl sm:text-3xl font-extrabold text-gray-900">50K+</div>
                                <div class="text-xs sm:text-sm text-gray-500 mt-1">Produk Terkelola</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl sm:text-3xl font-extrabold text-gray-900">99.9%</div>
                                <div class="text-xs sm:text-sm text-gray-500 mt-1">Uptime</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl sm:text-3xl font-extrabold text-gray-900">#1</div>
                                <div class="text-xs sm:text-sm text-gray-500 mt-1">di Indonesia</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Features Section --}}
            <section id="fitur" class="py-20 lg:py-28 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto">
                        <span class="inline-block px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold tracking-wide mb-4 border border-indigo-100">FITUR</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                            Semua yang Anda Butuhkan untuk<br/>
                            <span class="text-indigo-600">Mengelola Stok</span>
                        </h2>
                        <p class="mt-4 text-gray-500 leading-relaxed">
                            fitur lengkap yang dirancang untuk memudahkan bisnis Anda dalam mengelola inventaris.
                        </p>
                    </div>

                    <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                        {{-- Feature 1 --}}
                        <div class="group relative p-6 lg:p-8 rounded-2xl border border-gray-100 bg-white hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-50 transition-all duration-300">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-5 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Stok In / Out</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                                Catat setiap barang masuk dan keluar dengan mudah. Riwayat mutasi tersimpan rapi dan bisa diakses kapan saja.
                            </p>
                        </div>

                        {{-- Feature 2 --}}
                        <div class="group relative p-6 lg:p-8 rounded-2xl border border-gray-100 bg-white hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-50 transition-all duration-300">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-5 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Manajemen Pengiriman</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                                Buat dan pantau pengiriman barang ke berbagai tujuan. Lacak status dari draft, dikirim, hingga selesai.
                            </p>
                        </div>

                        {{-- Feature 3 --}}
                        <div class="group relative p-6 lg:p-8 rounded-2xl border border-gray-100 bg-white hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-50 transition-all duration-300">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-5 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Retur Barang</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                                Tangani retur barang dengan sistem approval. Proses pengembalian jadi lebih terstruktur dan terdokumentasi.
                            </p>
                        </div>

                        {{-- Feature 4 --}}
                        <div class="group relative p-6 lg:p-8 rounded-2xl border border-gray-100 bg-white hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-50 transition-all duration-300">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-5 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4v16h18V4H3zm2 4h14v2H5V8zm0 4h14v2H5v-2zm0 4h14v2H5v-2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Laporan Lengkap</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                                Lihat laporan stok, mutasi, dan pengiriman dalam tampian yang jelas. Membantu Anda mengambil keputusan bisnis.
                            </p>
                        </div>

                        {{-- Feature 5 --}}
                        <div class="group relative p-6 lg:p-8 rounded-2xl border border-gray-100 bg-white hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-50 transition-all duration-300">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-5 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Realtime Update</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                                Dapatkan update stok secara realtime. Setiap perubahan langsung terlihat tanpa perlu refresh halaman.
                            </p>
                        </div>

                        {{-- Feature 6 --}}
                        <div class="group relative p-6 lg:p-8 rounded-2xl border border-gray-100 bg-white hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-50 transition-all duration-300">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-5 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Multi Pengguna</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                                Dukung tim Anda dengan akses multi-pengguna. Atur peran dan hak akses setiap anggota tim.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- How It Works --}}
            <section class="py-20 lg:py-28 bg-gradient-to-b from-gray-50 to-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto">
                        <span class="inline-block px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold tracking-wide mb-4 border border-indigo-100">CARA KERJA</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                            Mulai dalam <span class="text-indigo-600">3 Langkah</span> Mudah
                        </h2>
                    </div>

                    <div class="mt-14 grid sm:grid-cols-3 gap-8 lg:gap-12 relative">
                        {{-- Connector line (desktop) --}}
                        <div class="hidden lg:block absolute top-16 left-[calc(16.667%+1.5rem)] right-[calc(16.667%+1.5rem)] h-0.5 bg-gradient-to-r from-indigo-200 via-indigo-300 to-indigo-200"></div>

                        {{-- Step 1 --}}
                        <div class="relative text-center">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white text-xl font-extrabold flex items-center justify-center mx-auto shadow-lg shadow-indigo-200 relative z-10">1</div>
                            <h3 class="mt-6 text-lg font-bold text-gray-900">Daftar Akun</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                                Daftar gratis dalam 1 menit. Tidak perlu kartu kredit. Cukup email dan password.
                            </p>
                        </div>

                        {{-- Step 2 --}}
                        <div class="relative text-center">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white text-xl font-extrabold flex items-center justify-center mx-auto shadow-lg shadow-indigo-200 relative z-10">2</div>
                            <h3 class="mt-6 text-lg font-bold text-gray-900">Tambah Produk</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                                Masukkan data produk Anda. Atur kategori, harga, dan stok awal dengan cepat.
                            </p>
                        </div>

                        {{-- Step 3 --}}
                        <div class="relative text-center">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white text-xl font-extrabold flex items-center justify-center mx-auto shadow-lg shadow-indigo-200 relative z-10">3</div>
                            <h3 class="mt-6 text-lg font-bold text-gray-900">Mulai Kelola</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                                Catat mutasi stok, buat pengiriman, dan pantau semuanya secara realtime.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- CTA Section --}}
            <section class="py-20 lg:py-28 bg-white">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-3xl p-8 sm:p-12 lg:p-16 shadow-2xl shadow-indigo-200">
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                            Siap Mengelola Stok<br/>
                            dengan Lebih Baik?
                        </h2>
                        <p class="mt-4 text-lg text-indigo-100 leading-relaxed max-w-xl mx-auto">
                            Bergabung dengan ribuan bisnis lain yang sudah menggunakan SimpleStock untuk mengelola inventaris mereka.
                        </p>
                        @if (Route::has('login') && !Auth::check())
                            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                                <a href="{{ route('register') }}"
                                   class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-indigo-700 font-bold rounded-xl hover:bg-indigo-50 shadow-xl transition-all text-base">
                                    Daftar Gratis Sekarang
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center px-8 py-3.5 border-2 border-indigo-400 text-white font-semibold rounded-xl hover:bg-indigo-700/50 transition-all text-base">
                                    Masuk
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Footer --}}
            <footer class="bg-gray-900 text-gray-400">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                        <div class="sm:col-span-2 lg:col-span-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-indigo-500 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    </svg>
                                </div>
                                <span class="font-extrabold text-xl text-white tracking-tight">{{ config('app.name') }}</span>
                            </div>
                            <p class="mt-4 text-sm leading-relaxed max-w-md">
                                Solusi manajemen stok yang sederhana, cepat, dan powerful untuk UKM di Indonesia. Catat, pantau, dan kelola inventaris Anda secara realtime.
                            </p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white text-sm tracking-wide uppercase">Fitur</h4>
                            <ul class="mt-4 space-y-3 text-sm">
                                <li><a href="#fitur" class="hover:text-white transition-colors">Stok In / Out</a></li>
                                <li><a href="#fitur" class="hover:text-white transition-colors">Pengiriman</a></li>
                                <li><a href="#fitur" class="hover:text-white transition-colors">Retur Barang</a></li>
                                <li><a href="#fitur" class="hover:text-white transition-colors">Laporan</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white text-sm tracking-wide uppercase">Perusahaan</h4>
                            <ul class="mt-4 space-y-3 text-sm">
                                <li><a href="#" class="hover:text-white transition-colors">Tentang</a></li>
                                <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                                <li><a href="#" class="hover:text-white transition-colors">Kontak</a></li>
                                <li><a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-10 pt-8 border-t border-gray-800 text-sm text-center sm:text-left">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
