<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#4338ca">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">

        <title>SimpleStockApp</title>

        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234F46E5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z'/%3E%3C/svg%3E">
        <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('sidebar', {
                    collapsed: false,
                    mobileOpen: false,
                    toggle() {
                        if (window.innerWidth < 1024) {
                            this.mobileOpen = !this.mobileOpen;
                        } else {
                            this.collapsed = !this.collapsed;
                        }
                    },
                    closeMobile() {
                        this.mobileOpen = false;
                    }
                });
                Alpine.store('toast', {
                    show: false,
                    message: '',
                    type: 'success',
                    progress: 100,
                    interval: null,
                    open(message, type = 'success', duration = 5000) {
                        if (this.interval) clearInterval(this.interval);
                        this.show = true;
                        this.message = message;
                        this.type = type;
                        this.progress = 100;
                        const step = 100 / (duration / 50);
                        this.interval = setInterval(() => {
                            this.progress = Math.max(0, this.progress - step);
                            if (this.progress <= 0) {
                                clearInterval(this.interval);
                                this.interval = null;
                                this.show = false;
                            }
                        }, 50);
                    },
                    close() {
                        if (this.interval) {
                            clearInterval(this.interval);
                            this.interval = null;
                        }
                        this.show = false;
                    }
                });
            });
        </script>
        @php
            $route = request()->route()?->getName() ?? '';
            $isActive = fn($pattern) => $route && fnmatch($pattern, $route);
        @endphp
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ showLogoutModal: false, showUserMenu: false }" class="min-h-screen bg-gray-50"
             @keydown.escape.window="$store.sidebar.closeMobile()">

            {{-- Mobile overlay --}}
            <div x-show="$store.sidebar.mobileOpen" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-30 bg-black/40 lg:hidden" @click="$store.sidebar.closeMobile()"></div>

            {{-- Sidebar --}}
            <aside class="fixed inset-y-0 left-0 z-40 bg-indigo-700 flex flex-col transition-all duration-300 ease-out lg:translate-x-0 w-64"
                   :class="{
                       'w-64': !$store.sidebar.collapsed,
                       'w-16': $store.sidebar.collapsed,
                       'translate-x-0': $store.sidebar.mobileOpen,
                       '-translate-x-full': !$store.sidebar.mobileOpen
                   }"
                   x-on:resize.window="if(window.innerWidth >= 1024) $store.sidebar.mobileOpen = false">

                {{-- Logo & Toggle --}}
                <div class="flex items-center h-16 border-b border-indigo-600" :class="$store.sidebar.collapsed ? 'justify-center px-2' : 'gap-1 px-2'">
                    <div @click="if($store.sidebar.collapsed) $store.sidebar.toggle()"
                         class="flex items-center shrink-0" :class="$store.sidebar.collapsed ? 'cursor-pointer' : ''">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            </svg>
                        </div>
                        <span x-show="!$store.sidebar.collapsed" x-cloak class="text-white font-bold text-base tracking-tight whitespace-nowrap ml-3">{{ config('app.name') }}</span>
                    </div>
                    <button @click="$store.sidebar.toggle()" x-show="!$store.sidebar.collapsed"
                            class="flex items-center justify-center w-10 h-10 rounded-lg text-indigo-200/80 hover:text-white hover:bg-indigo-500/20 transition-all duration-150 shrink-0 ml-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                {{-- Nav --}}
                <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto sidebar-scroll">
                    <template x-if="!$store.sidebar.collapsed">
                        <p class="px-3 pb-2 text-xs font-semibold text-indigo-300/70 uppercase tracking-widest">Menu Utama</p>
                    </template>

                    <x-sidebar-link :href="route('dashboard')" :active="fnmatch('dashboard', $route)" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />'>Dashboard</x-sidebar-link>

                    <template x-if="!$store.sidebar.collapsed">
                        <div>
                            <div class="my-4 border-t border-indigo-600/50"></div>
                            <p class="px-3 pb-2 pt-1.5 text-xs font-semibold text-indigo-300/70 uppercase tracking-widest">Inventaris</p>
                        </div>
                    </template>

                    <x-sidebar-link :href="route('products.index')" :active="fnmatch('products.*', $route)" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'>Produk</x-sidebar-link>
                    <x-sidebar-link :href="route('stock.index')" :active="fnmatch('stock.*', $route)" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>'>Mutasi Stok</x-sidebar-link>

                    <template x-if="!$store.sidebar.collapsed">
                        <div>
                            <div class="my-4 border-t border-indigo-600/50"></div>
                            <p class="px-3 pb-2 pt-1.5 text-xs font-semibold text-indigo-300/70 uppercase tracking-widest">Logistik</p>
                        </div>
                    </template>

                    <x-sidebar-link :href="route('shipments.index')" :active="fnmatch('shipments.*', $route)" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-1m0 0l2 1m-2-1v10a1 1 0 002 1h12a1 1 0 002-1v-9a1 1 0 00-1-1h-5m-8 4h12"/>'>Pengiriman</x-sidebar-link>
                    <x-sidebar-link :href="route('returns.index')" :active="fnmatch('returns.*', $route)" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>'>Retur</x-sidebar-link>

                    @can('admin')
                    <template x-if="!$store.sidebar.collapsed">
                        <div>
                            <div class="my-4 border-t border-indigo-600/50"></div>
                            <p class="px-3 pb-2 pt-1.5 text-xs font-semibold text-indigo-300/70 uppercase tracking-widest">Pengaturan</p>
                        </div>
                    </template>
                    <x-sidebar-link :href="route('users.index')" :active="fnmatch('users.*', $route)" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>'>Manajemen Pengguna</x-sidebar-link>
                    @endif
                </nav>

                {{-- Logout --}}
                <div class="border-t border-indigo-600 px-3 py-3">
                    <button @click="showLogoutModal = true"
                            class="flex items-center rounded-lg text-sm font-medium text-indigo-200/80 hover:text-white hover:bg-indigo-500/20 transition-all duration-150 w-full"
                            :class="$store.sidebar.collapsed ? 'justify-center h-10 w-10 mx-auto' : 'gap-3 px-3 py-2.5'">
                        <svg class="w-5 h-5 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span x-show="!$store.sidebar.collapsed" x-cloak>Logout</span>
                    </button>
                </div>
            </aside>

            {{-- Main --}}
            <div class="flex-1 transition-all duration-300 ease-out"
                 :class="$store.sidebar.collapsed ? 'lg:ml-16' : 'lg:ml-64'">
                {{-- Top bar --}}
                <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-20">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="$store.sidebar.toggle()"
                                class="lg:hidden p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="text-lg lg:text-xl font-semibold text-gray-900 truncate">{{ $header ?? 'Dashboard' }}</h1>
                    </div>

                    <div class="relative shrink-0" @click.outside="showUserMenu = false">
                        <button @click="showUserMenu = !showUserMenu"
                                class="flex items-center gap-2 lg:gap-3 px-2 lg:px-3 py-2 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="text-sm text-left hidden sm:block">
                                <p class="font-medium text-gray-900 leading-tight truncate max-w-[120px]">{{ Auth::user()->name }}</p>
                                <p class="text-gray-500 text-xs leading-tight hidden md:block truncate max-w-[150px]">{{ Auth::user()->email }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0 hidden sm:block" :class="{ 'rotate-180': showUserMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="showUserMenu" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute right-0 top-full mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg shadow-gray-200/50 overflow-hidden">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profil Saya
                            </a>
                            <hr class="border-gray-100">
                            <button @click="showLogoutModal = true; showUserMenu = false"
                                    class="flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </button>
                        </div>
                    </div>
                </header>

                {{-- Page content --}}
                <main class="p-4 lg:p-8">
                    {{ $slot }}
                </main>
            </div>

        {{-- Logout Modal --}}
        <div x-show="showLogoutModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-white/10 backdrop-blur-sm"></div>
        <div x-show="showLogoutModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed inset-0 z-50 flex items-center justify-center" @click.self="showLogoutModal = false" @keydown.escape.window="showLogoutModal = false">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi Logout</h3>
                <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin keluar?</p>

                <div class="flex justify-center gap-3">
                    <button @click="showLogoutModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                    <form method="POST" action="{{ route('logout') }}" @submit="showLogoutModal = false">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Toast/Alert --}}
        <div x-show="$store.toast.show" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed top-4 right-4 z-50 overflow-hidden rounded-xl shadow-lg border text-sm font-medium max-w-[90vw]"
             :class="$store.toast.type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'">
            <div class="flex items-center gap-3 px-5 py-3">
                <span x-text="$store.toast.message"></span>
                <button @click="$store.toast.close()" class="ml-2 text-lg leading-none hover:opacity-70 shrink-0" :class="$store.toast.type === 'success' ? 'text-green-700' : 'text-red-700'">&times;</button>
            </div>
            <div class="h-1 w-full bg-gray-200/50">
                <div class="h-full transition-all duration-[50ms] linear"
                     :class="$store.toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
                     :style="'width: ' + $store.toast.progress + '%'"></div>
            </div>
        </div>
        </div>
    </body>
</html>