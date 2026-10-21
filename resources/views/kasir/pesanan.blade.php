@extends('layouts.app')

@section('title', 'Input Pesanan')

@section('content')
<div id="pesanan-page" class="flex gap-6 h-full" x-data="pesananState()">
    {{-- Left Panel: Menu Grid --}}
    <div class="w-[70%] flex flex-col">
        {{-- Search & Filter --}}
        <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
            <div class="flex gap-3">
                <div class="flex-1 relative">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="search" @input="filterMenus()" placeholder="Cari menu..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600">
                </div>
            </div>
            <div class="flex gap-2 mt-3 flex-wrap">
                <button @click="kategoriFilter = ''; filterMenus()" :class="kategoriFilter === '' ? 'bg-emerald-900 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="px-4 py-1.5 rounded-full text-sm font-medium transition">
                    Semua
                </button>
                <template x-for="kat in kategoris" :key="kat.id">
                    <button @click="kategoriFilter = kat.id; filterMenus()" :class="kategoriFilter === kat.id ? 'bg-emerald-900 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="px-4 py-1.5 rounded-full text-sm font-medium transition" x-text="kat.nama_kategori">
                    </button>
                </template>
            </div>
        </div>

        {{-- Menu Grid --}}
        <div class="flex-1 overflow-y-auto">
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <template x-for="menu in filteredMenus" :key="menu.id">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden cursor-pointer hover:shadow-md transition relative" @click="addItem(menu)">
                        {{-- Foto --}}
                        <div class="h-32 bg-gray-100 relative">
                            <template x-if="menu.foto">
                                <img :src="'/storage/' + menu.foto" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!menu.foto">
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </template>
                            {{-- Stok badge --}}
                            <div class="absolute top-2 right-2">
                                <span :class="menu.stok === 0 ? 'bg-red-500 text-white' : menu.stok <= 5 ? 'bg-yellow-400 text-yellow-900' : 'bg-emerald-900/80 text-white'" class="text-xs font-bold px-2 py-1 rounded" x-text="menu.stok === 0 ? 'Habis' : 'Stok ' + menu.stok"></span>
                            </div>
                            {{-- Habis overlay --}}
                            <template x-if="menu.stok === 0">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                    <span class="bg-red-500 text-white font-bold px-3 py-1 rounded-xl text-sm">HABIS</span>
                                </div>
                            </template>
                        </div>
                        {{-- Info --}}
                        <div class="p-3">
                            <h3 class="font-semibold text-sm text-gray-800 truncate" x-text="menu.nama_menu"></h3>
                            <p class="text-emerald-800 font-bold text-sm mt-1" x-text="rupiah(menu.harga)"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

   {{-- Right Panel: Cart --}}
    <div class="w-[30%] flex flex-col h-full">
        <div class="bg-white rounded-xl shadow-sm flex flex-col h-full">
            {{-- Tabs --}}
            <div class="flex shrink-0">
                
                <!-- Tab Pesanan Baru -->
                <button @click="panelTab = 'cart'" 
                        :class="panelTab === 'cart' ? 'border-b-2 border-emerald-900 text-emerald-900 font-bold' : 'border-b border-gray-200 text-gray-500 hover:text-gray-700'" 
                        class="flex-1 px-4 py-3 text-sm transition-colors">
                    Pesanan Baru
                </button>
                
                <!-- Tab Pending -->
                <button @click="panelTab = 'pending'" 
                        :class="panelTab === 'pending' ? 'border-b-2 border-orange-500 text-orange-600 font-bold' : 'border-b border-gray-200 text-gray-500 hover:text-gray-700'" 
                        class="flex-1 px-4 py-3 text-sm transition-colors relative">
                    Pending
                    <template x-if="pendingTransaksis.length > 0">
                        <span class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shadow-sm" x-text="pendingTransaksis.length"></span>
                    </template>
                </button>
                
            </div>

            <div class="flex-1 min-h-0 overflow-hidden rounded-b-xl">
            {{-- Tab: Cart --}}
            <div x-show="panelTab === 'cart'" x-cloak class="flex flex-col flex-1 h-full">
                    
                    {{-- Tipe Pesanan (DITAMBAH shrink-0 agar terkunci di atas) --}}
                    {{-- Tipe Pesanan --}}
                    <div class="p-4 border-b shrink-0">
                        
                        <!-- Tombol Makan di Tempat & Bungkus -->
                        <div class="flex gap-2 mb-4">
                            <button @click="tipePesanan = 'dine_in'" :class="tipePesanan === 'dine_in' ? 'bg-emerald-900 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="flex-1 px-3 py-2 rounded-xl text-sm font-medium transition">
                                Makan di Tempat
                            </button>
                            <button @click="tipePesanan = 'bungkus'; nomorMeja = ''" :class="tipePesanan === 'bungkus' ? 'bg-emerald-900 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="flex-1 px-3 py-2 rounded-xl text-sm font-medium transition">
                                Bungkus
                            </button>
                        </div>
                        
                        <!-- Nomor Meja (HANYA MUNCUL JIKA MAKAN DI TEMPAT) -->
                        <template x-if="tipePesanan === 'dine_in'">
                            <div class="w-full overflow-hidden mb-3">
                                <label class="block text-sm font-medium text-gray-800 mb-2">Nomor Meja</label>
                                
                                <div class="flex overflow-x-auto gap-4 pb-2 snap-x snap-mandatory scrollbar-hide w-full" style="scrollbar-width: none; -ms-overflow-style: none;">
                                    @php
                                        $totalMeja = 20; 
                                        $grupMeja = array_chunk(range(1, $totalMeja), 10);
                                    @endphp

                                    @foreach ($grupMeja as $grup)
                                        <div class="w-full flex-shrink-0 snap-center">
                                            <div class="grid grid-cols-5 gap-2">
                                                @foreach ($grup as $i)
                                                <button type="button" 
                                                        @click="nomorMeja = '{{ $i }}'"
                                                        :class="nomorMeja == '{{ $i }}' ? 'bg-emerald-900 text-white shadow-sm' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                                                        class="w-full h-10 rounded text-sm font-medium transition-all flex items-center justify-center">
                                                    {{ $i }}
                                                </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </template>

                        <!-- Input Nama Pelanggan (SELALU MUNCUL UNTUK KEDUANYA) -->
                        <div>
                            <input type="text" 
                                   x-model="namaPelanggan" 
                                   placeholder="Nama Pelanggan" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm transition-colors focus:outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20">
                        </div>
                        </template>
                    </div>

                    {{-- Item List (Tengah - Scrollable) --}}
                    <div class="flex-1 overflow-y-auto p-4 space-y-3 min-h-0">
                        <template x-for="(item, index) in items" :key="item.menu_id">
                            <div class="flex items-start gap-2 pb-3 border-b border-gray-100 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate" x-text="item.nama"></p>
                                    <p class="text-xs text-gray-500" x-text="rupiah(item.harga)"></p>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button @click="decreaseQty(index)" class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-sm transition">-</button>
                                    <span class="w-8 text-center text-sm font-semibold" x-text="item.qty"></span>
                                    <button @click="increaseQty(index)" class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-sm transition">+</button>
                                </div>
                                <div class="text-right min-w-[80px] shrink-0">
                                    <p class="text-sm font-semibold text-gray-800" x-text="rupiah(item.subtotal)"></p>
                                    <button @click="removeItem(index)" class="text-xs text-red-600 hover:text-red-800 font-medium mt-1">Hapus</button>
                                </div>
                            </div>
                        </template>
                        <template x-if="items.length === 0">
                            <div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                                <p class="text-sm">Belum ada item</p>
                            </div>
                        </template>
                    </div>

                    {{-- Total & Action (DITAMBAH shrink-0 bg-white mt-auto agar terkunci di bawah) --}}
                    <div class="p-4 border-t shrink-0 bg-white mt-auto z-10">
                        <div class="flex justify-between items-center mb-4">
                            
                            <!-- BAGIAN YANG DIRUBAH: Total dan (items) dipisah -->
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-lg font-bold text-gray-800">Total</span>
                                <template x-if="items.length > 0">
                                    <span class="text-sm font-medium text-gray-500" x-text="'(' + items.reduce((total, item) => total + item.qty, 0) + ' items)'"></span>
                                </template>
                            </div>

                            <span class="text-lg font-bold text-emerald-800" x-text="rupiah(total)"></span>
                        </div>
                        <button @click="prosesPembayaran()" :disabled="items.length === 0" :class="items.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-emerald-900 hover:bg-emerald-800 shadow-md'" class="w-full text-white font-semibold py-3 rounded-xl transition">
                            Proses Pembayaran
                        </button>
                    </div>
                </div>
                    

            {{-- Tab: Pending --}}
            <div x-show="panelTab === 'pending'" x-cloak class="flex flex-col flex-1 min-h-0">
                <div class="flex-1 overflow-y-auto p-4 space-y-2 min-h-0">
                        <template x-for="pt in pendingTransaksis" :key="pt.id">
                            <div class="bg-white border border-orange-200 rounded-xl overflow-hidden hover:shadow-sm transition">
                                {{-- Header --}}
                                <div class="bg-orange-500 text-white px-3 py-2 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm" x-text="'#' + (pt.pesanans[0]?.nomor_pesanan || pt.id)"></span>
                                        <template x-if="pt.pesanans[0]?.nomor_meja">
                                            <span class="text-[11px] bg-white/20 px-2 py-0.5 rounded-full" x-text="'Meja ' + pt.pesanans[0].nomor_meja"></span>
                                        </template>
                                        <span class="text-[11px] bg-white/20 px-2 py-0.5 rounded-full" x-text="pt.pesanans[0]?.tipe_pesanan === 'dine_in' ? 'Dine In' : 'Bungkus'"></span>
                                    </div>
                                    <span class="text-xs opacity-80" x-text="new Date(pt.created_at).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})"></span>
                                </div>
                                {{-- Items --}}
                                <div class="px-3 py-2 space-y-1">
                                    <template x-for="dp in pt.pesanans[0]?.detail_pesanans || []" :key="dp.id">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-700 truncate" x-text="(dp.menu?.nama_menu || 'Menu') + ' x' + dp.qty"></span>
                                            <span class="font-semibold text-gray-800 ml-2" x-text="rupiah(dp.subtotal)"></span>
                                        </div>
                                    </template>
                                </div>
                                {{-- Status & Total --}}
                                <div class="px-3 py-2 border-t border-orange-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-medium text-orange-600 bg-orange-100 px-2 py-0.5 rounded-full capitalize" x-text="pt.pesanans[0]?.status || '-'"></span>
                                    </div>
                                    <span class="text-sm font-bold text-emerald-800" x-text="rupiah(pt.total_harga)"></span>
                                </div>
                                {{-- Action --}}
                                <div class="px-3 pb-3">
                                    <button @click="bayarPending(pt)" class="w-full px-3 py-2 bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-semibold rounded-lg transition">
                                        Bayar Sekarang
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template x-if="pendingTransaksis.length === 0">
                            <div class="flex flex-col items-center justify-center h-full text-gray-400 py-12">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm">Tidak ada pesanan pending</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Modal --}}
    <template x-teleport="body">
        <div x-show="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-black/50" @click="showPaymentModal = false"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-xl relative z-10 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Pembayaran</h3>

                <div class="bg-emerald-50 rounded-xl p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Harga</span>
                        <span class="text-xl font-bold text-emerald-800" x-text="rupiah(total)"></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                    <div class="flex gap-2">
                        <button @click="tipeBayar = 'tunai'" :class="tipeBayar === 'tunai' ? 'bg-emerald-900 text-white ring-2 ring-emerald-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Tunai
                        </button>
                        <button @click="tipeBayar = 'qris'; bayarValue = total; bayarDisplay = rupiah(total)" :class="tipeBayar === 'qris' ? 'bg-emerald-900 text-white ring-2 ring-emerald-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            QRIS
                        </button>
                        <button @click="tipeBayar = 'bayar_nanti'" :class="tipeBayar === 'bayar_nanti' ? 'bg-orange-500 text-white ring-2 ring-orange-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Bayar Nanti
                        </button>
                    </div>
                </div>

                <template x-if="tipeBayar !== 'bayar_nanti'">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Bayar</label>
                        <input type="text" x-model="bayarDisplay" @input="parseBayar($event)" @focus="$event.target.select()" :readonly="tipeBayar === 'qris'" :class="tipeBayar === 'qris' ? 'bg-gray-100 cursor-not-allowed' : ''" placeholder="Masukkan nominal" class="w-full px-4 py-3 text-lg font-bold border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600">
                    </div>
                </template>

                <template x-if="tipeBayar === 'tunai'">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quick Amount</label>
                        <div class="grid grid-cols-5 gap-2">
                            <button @click="setBayarQuick(50000)" :class="bayarValue === 50000 ? 'bg-emerald-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'" class="px-2 py-2 rounded-lg text-sm font-semibold transition">50rb</button>
                            <button @click="setBayarQuick(100000)" :class="bayarValue === 100000 ? 'bg-emerald-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'" class="px-2 py-2 rounded-lg text-sm font-semibold transition">100rb</button>
                            <button @click="setBayarQuick(150000)" :class="bayarValue === 150000 ? 'bg-emerald-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'" class="px-2 py-2 rounded-lg text-sm font-semibold transition">150rb</button>
                            <button @click="setBayarQuick(200000)" :class="bayarValue === 200000 ? 'bg-emerald-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'" class="px-2 py-2 rounded-lg text-sm font-semibold transition">200rb</button>
                            <button @click="setBayarQuick(500000)" :class="bayarValue === 500000 ? 'bg-emerald-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'" class="px-2 py-2 rounded-lg text-sm font-semibold transition">500rb</button>
                        </div>
                    </div>
                </template>

                <template x-if="tipeBayar !== 'bayar_nanti'">
                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Kembalian</span>
                            <span class="text-xl font-bold" :class="kembalian >= 0 ? 'text-emerald-700' : 'text-red-600'" x-text="rupiah(kembalian)"></span>
                        </div>
                    </div>
                </template>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cetak Struk</label>
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" x-model="cetakPelanggan" class="w-4 h-4 accent-emerald-900 rounded border-gray-300 focus:ring-emerald-500" :disabled="tipeBayar === 'bayar_nanti'">
                            <div>
                                <span class="text-sm font-medium text-gray-800">Struk Pelanggan</span>
                                <p class="text-xs text-gray-500">Rincian untuk pelanggan</p>
                            </div>
                        </label>
                        <label class="flex-1 flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" x-model="cetakDapur" class="w-4 h-4 accent-emerald-900 rounded border-gray-300 focus:ring-emerald-500" :disabled="tipeBayar === 'bayar_nanti'">
                            <div>
                                <span class="text-sm font-medium text-gray-800">Struk Dapur</span>
                                <p class="text-xs text-gray-500">Catatan untuk dapur</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="showPaymentModal = false" class="flex-1 px-4 py-2.5 border border-emerald-900 rounded-xl text-emerald-900 hover:bg-emerald-50 font-medium transition">Batal</button>
                    <button @click="konfirmasiBayar()" :disabled="(tipeBayar !== 'bayar_nanti' && (bayarValue < total || !cetakDapur))" :class="(tipeBayar !== 'bayar_nanti' && (bayarValue < total || !cetakDapur)) ? 'bg-gray-300 cursor-not-allowed' : (tipeBayar === 'bayar_nanti' ? 'bg-orange-500 hover:bg-orange-600' : 'bg-emerald-900 hover:bg-emerald-800')" class="flex-1 px-6 py-2.5 text-white rounded-xl font-semibold transition">
                        <span x-text="tipeBayar === 'bayar_nanti' ? 'Kirim ke Dapur' : 'Konfirmasi Bayar'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Bayar Pending Modal --}}
    <template x-teleport="body">
        <div x-show="showBayarPendingModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-black/50" @click="showBayarPendingModal = false"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-xl relative z-10 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Bayar Pesanan</h3>

                <template x-if="pendingTarget">
                    <div class="bg-orange-50 rounded-xl p-4 mb-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-500">No. Pesanan</p>
                                <p class="text-lg font-bold text-gray-800" x-text="'#' + (pendingTarget.pesanans[0]?.nomor_pesanan || pendingTarget.id)"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Total Harga</p>
                                <p class="text-lg font-bold text-emerald-800" x-text="rupiah(pendingTarget.total_harga)"></p>
                            </div>
                        </div>
                        <div class="flex gap-4 mt-2 text-xs text-gray-500">
                            <span x-text="pendingTarget.pesanans[0]?.tipe_pesanan === 'dine_in' ? 'Makan di Tempat' : 'Bungkus'"></span>
                            <template x-if="pendingTarget.pesanans[0]?.nomor_meja">
                                <span x-text="'Meja ' + pendingTarget.pesanans[0].nomor_meja"></span>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                    <div class="flex gap-2">
                        <button @click="tipeBayar = 'tunai'" :class="tipeBayar === 'tunai' ? 'bg-emerald-900 text-white ring-2 ring-emerald-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Tunai
                        </button>
                        <button @click="tipeBayar = 'qris'; bayarValue = pendingTarget.total_harga; bayarDisplay = rupiah(pendingTarget.total_harga)" :class="tipeBayar === 'qris' ? 'bg-emerald-900 text-white ring-2 ring-emerald-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            QRIS
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Bayar</label>
                    <input type="text" x-model="bayarDisplay" @input="parseBayar($event)" @focus="$event.target.select()" :readonly="tipeBayar === 'qris'" :class="tipeBayar === 'qris' ? 'bg-gray-100 cursor-not-allowed' : ''" placeholder="Masukkan nominal" class="w-full px-4 py-3 text-lg font-bold border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600">
                </div>

                <template x-if="tipeBayar === 'tunai'">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quick Amount</label>
                        <div class="grid grid-cols-5 gap-2">
                            <button @click="setBayarQuick(50000)" class="px-2 py-2 rounded-lg text-sm font-semibold transition" :class="bayarValue === 50000 ? 'bg-emerald-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'">50rb</button>
                            <button @click="setBayarQuick(100000)" class="px-2 py-2 rounded-lg text-sm font-semibold transition" :class="bayarValue === 100000 ? 'bg-emerald-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'">100rb</button>
                            <button @click="setBayarQuick(150000)" class="px-2 py-2 rounded-lg text-sm font-semibold transition" :class="bayarValue === 150000 ? 'bg-emerald-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'">150rb</button>
                            <button @click="setBayarQuick(200000)" class="px-2 py-2 rounded-lg text-sm font-semibold transition" :class="bayarValue === 200000 ? 'bg-emerald-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'">200rb</button>
                            <button @click="setBayarQuick(500000)" class="px-2 py-2 rounded-lg text-sm font-semibold transition" :class="bayarValue === 500000 ? 'bg-emerald-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'">500rb</button>
                        </div>
                    </div>
                </template>

                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Kembalian</span>
                        <span class="text-xl font-bold" :class="(bayarValue - (pendingTarget?.total_harga || 0)) >= 0 ? 'text-emerald-700' : 'text-red-600'" x-text="rupiah(bayarValue - (pendingTarget?.total_harga || 0))"></span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="showBayarPendingModal = false; pendingTarget = null" class="flex-1 px-4 py-2.5 border border-emerald-900 rounded-xl text-emerald-900 hover:bg-emerald-50 font-medium transition">Batal</button>
                    <button @click="konfirmasiBayarPending()" :disabled="bayarValue < (pendingTarget?.total_harga || 0)" :class="bayarValue < (pendingTarget?.total_harga || 0) ? 'bg-gray-300 cursor-not-allowed' : 'bg-emerald-900 hover:bg-emerald-800'" class="flex-1 px-6 py-2.5 text-white rounded-xl font-semibold transition">
                        Konfirmasi Bayar
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Struk Modal --}}
    <template x-teleport="body">
        <div x-show="showStrukModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-black/50" @click="showStrukModal = false"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm relative z-10 p-6 max-h-[90vh] overflow-y-auto" id="struk-area">
                {{-- Struk Pelanggan --}}
                <div id="struk-pelanggan" x-show="cetakPelanggan">
                    <div class="text-center mb-4 border-b pb-4">
                        <h4 class="font-bold text-lg" x-text="strukPelanggan.nama_usaha"></h4>
                        <p class="text-xs text-gray-500" x-text="strukPelanggan.alamat"></p>
                        <p class="text-xs text-gray-500" x-text="strukPelanggan.kontak"></p>
                    </div>

                    <div class="text-xs text-gray-600 mb-3 space-y-1">
                        <div class="flex justify-between">
                            <span>No. Pesanan</span>
                            <span class="font-semibold" x-text="strukPelanggan.nomor_pesanan"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tipe</span>
                            <span x-text="strukPelanggan.tipe_pesanan"></span>
                        </div>
                        <template x-if="strukPelanggan.nomor_meja">
                            <div class="flex justify-between">
                                <span>Meja</span>
                                <span x-text="'Meja ' + strukPelanggan.nomor_meja"></span>
                            </div>
                        </template>
                        <div class="flex justify-between">
                            <span>Tanggal</span>
                            <span x-text="strukPelanggan.datetime"></span>
                        </div>
                    </div>

                    <div class="border-t border-b py-2 mb-3 space-y-1">
                        <template x-for="item in strukPelanggan.items" :key="item.nama">
                            <div class="flex justify-between text-sm">
                                <span class="flex-1" x-text="item.nama"></span>
                                <span class="text-gray-500 mx-2" x-text="item.qty + 'x'"></span>
                                <span class="font-semibold" x-text="rupiah(item.subtotal)"></span>
                            </div>
                        </template>
                    </div>

                    <div class="text-sm space-y-1 mb-4">
                        <div class="flex justify-between font-bold">
                            <span>Total</span>
                            <span x-text="rupiah(strukPelanggan.total_harga)"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Bayar</span>
                            <span x-text="rupiah(strukPelanggan.bayar)"></span>
                        </div>
                        <div class="flex justify-between text-emerald-700 font-semibold">
                            <span>Kembalian</span>
                            <span x-text="rupiah(strukPelanggan.kembalian)"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tipe Bayar</span>
                            <span class="capitalize" x-text="strukPelanggan.tipe_bayar"></span>
                        </div>
                    </div>

                    <div class="text-center text-xs text-gray-500 border-t pt-3">
                        <p>Kasir: <span x-text="strukPelanggan.kasir"></span></p>
                        <p class="mt-1">Terima kasih telah berbelanja</p>
                    </div>
                </div>

                {{-- Separator & Struk Dapur --}}
                <template x-if="cetakDapur">
                    <div id="struk-dapur-section">
                        <div x-show="cetakPelanggan" class="my-4 text-center">
                            <div class="border-t-2 border-dashed border-gray-400 mb-1"></div>
                            <p class="text-xs font-bold text-gray-500 tracking-widest">- - - - - - - - - - POTONG DISINI - - - - - - - - - -</p>
                            <div class="border-t-2 border-dashed border-gray-400 mt-1"></div>
                        </div>

                        <div id="struk-dapur">
                            <div class="text-center mb-4 border-b pb-4">
                                <h4 class="font-bold text-lg" x-text="strukDapur.nama_usaha"></h4>
                                <p class="text-xs text-gray-500 font-semibold mt-1">STRUK DAPUR / KITCHEN ORDER</p>
                            </div>

                            <div class="text-xs text-gray-600 mb-3 space-y-1">
                                <div class="flex justify-between">
                                    <span>No. Pesanan</span>
                                    <span class="font-semibold" x-text="strukDapur.nomor_pesanan"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Tipe</span>
                                    <span x-text="strukDapur.tipe_pesanan"></span>
                                </div>
                                <template x-if="strukDapur.nomor_meja">
                                    <div class="flex justify-between">
                                        <span>Meja</span>
                                        <span x-text="'Meja ' + strukDapur.nomor_meja"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between">
                                    <span>Tanggal</span>
                                    <span x-text="strukDapur.datetime"></span>
                                </div>
                            </div>

                            <div class="border-t border-b py-2 mb-4 space-y-1">
                                <template x-for="item in strukDapur.items" :key="item.nama">
                                    <div class="flex justify-between text-sm">
                                        <span class="flex-1 font-medium" x-text="item.nama"></span>
                                        <span class="font-bold text-emerald-800" x-text="item.qty + 'x'"></span>
                                    </div>
                                </template>
                            </div>

                            <div class="text-center text-xs text-gray-500 border-t pt-3">
                                <p>Terima kasih</p>
                                <p class="mt-1">Selamat Menikmati</p>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex gap-3 mt-4">
                    <button @click="cetakStruk()" class="flex-1 px-6 py-2.5 bg-emerald-900 text-white rounded-xl font-semibold hover:bg-emerald-800 transition">
                        Cetak Struk
                    </button>
                    <button @click="showStrukModal = false; resetPesanan()" class="flex-1 px-4 py-2.5 border border-emerald-900 rounded-xl text-emerald-900 hover:bg-emerald-50 font-medium transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

<style>
    main,
    #pesanan-page .overflow-y-auto {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    main::-webkit-scrollbar,
    #pesanan-page .overflow-y-auto::-webkit-scrollbar {
        display: none;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function rupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function pesananState() {
    return {
        menus: @json($menus),
        kategoris: @json($kategoris),
        filteredMenus: @json($menus),
        pendingTransaksis: @json($pendingTransaksis),
        panelTab: 'cart',
        search: '',
        kategoriFilter: '',
        items: [],
        tipePesanan: 'dine_in',
        nomorMeja: '',
        showPaymentModal: false,
        showStrukModal: false,
        showBayarPendingModal: false,
        tipeBayar: 'tunai',
        bayarDisplay: '',
        bayarValue: 0,
        cetakPelanggan: true,
        cetakDapur: true,
        strukPelanggan: {},
        strukDapur: {},
        pendingTarget: null,
        pollingInterval: null,

        init() {
            this.pollingInterval = setInterval(() => {
                Promise.all([
                    fetch('/kasir/menu-stok').then(r => r.json()),
                    fetch('/kasir/pesanan/pending-data').then(r => r.json()).catch(() => []),
                ]).then(([stok, pending]) => {
                    stok.forEach(fm => {
                        const old = this.menus.find(m => m.id === fm.id);
                        if (old) old.stok = fm.stok;
                    });
                    this.filterMenus();
                    this.pendingTransaksis = pending;
                }).catch(() => {});
            }, 10000);
        },

        filterMenus() {
            let result = this.menus;
            if (this.search) {
                const q = this.search.toLowerCase();
                result = result.filter(m => m.nama_menu.toLowerCase().includes(q));
            }
            if (this.kategoriFilter) {
                result = result.filter(m => m.kategori_id === this.kategoriFilter);
            }
            this.filteredMenus = result;
        },

        addItem(menu) {
            if (menu.stok === 0) {
                Swal.fire({
                    icon: 'error',
                    title: '"' + menu.nama_menu + '" sedang habis',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    iconColor: '#DC2626',
                    background: '#ffffff',
                });
                return;
            }
            const existing = this.items.find(i => i.menu_id === menu.id);
            if (existing) {
                if (existing.qty < menu.stok) {
                    existing.qty++;
                    existing.subtotal = existing.qty * existing.harga;
                }
            } else {
                this.items.push({
                    menu_id: menu.id,
                    nama: menu.nama_menu,
                    harga: parseFloat(menu.harga),
                    qty: 1,
                    subtotal: parseFloat(menu.harga),
                });
            }
        },

        increaseQty(index) {
            const item = this.items[index];
            const menu = this.menus.find(m => m.id === item.menu_id);
            if (item.qty < menu.stok) {
                item.qty++;
                item.subtotal = item.qty * item.harga;
            }
        },

        decreaseQty(index) {
            const item = this.items[index];
            if (item.qty > 1) {
                item.qty--;
                item.subtotal = item.qty * item.harga;
            } else {
                this.removeItem(index);
            }
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        get total() {
            return this.items.reduce((sum, item) => sum + item.subtotal, 0);
        },

        get kembalian() {
            return this.bayarValue - this.total;
        },

        parseBayar(event) {
            let val = event.target.value.replace(/[^0-9]/g, '');
            this.bayarValue = val ? parseInt(val) : 0;
            this.bayarDisplay = this.bayarValue ? rupiah(this.bayarValue) : '';
        },

        setBayarQuick(amount) {
            this.bayarValue = amount;
            this.bayarDisplay = rupiah(amount);
        },

        prosesPembayaran() {
            if (this.items.length === 0) return;
            if (this.tipePesanan === 'dine_in' && !this.nomorMeja) {
                toast('warning', 'Silakan isi nomor meja.');
                return;
            }
            this.showPaymentModal = true;
            this.tipeBayar = 'tunai';
            this.bayarDisplay = '';
            this.bayarValue = 0;
            this.cetakPelanggan = true;
            this.cetakDapur = true;
        },

        konfirmasiBayar() {
            if (this.tipeBayar === 'bayar_nanti') {
                const payload = {
                    items: this.items.map(i => ({ menu_id: i.menu_id, qty: i.qty })),
                    tipe_pesanan: this.tipePesanan,
                    nomor_meja: this.nomorMeja,
                    _token: '{{ csrf_token() }}',
                };

                fetch('{{ route("kasir.pesanan.bayar-nanti") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload),
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) { toast('error', data.message || 'Gagal.'); return; }
                    this.strukDapur = data.data.struk_dapur;
                    this.showPaymentModal = false;
                    this.cetakPelanggan = false;
                    this.cetakDapur = true;
                    this.showStrukModal = true;
                    toast('success', 'Pesanan #' + data.data.nomor_pesanan + ' dikirim ke dapur.');
                })
                .catch(() => toast('error', 'Terjadi kesalahan.'));
                return;
            }

            if (this.bayarValue < this.total) {
                toast('warning', 'Nominal bayar kurang dari total harga.');
                return;
            }
            if (!this.cetakDapur) {
                toast('warning', 'Struk dapur wajib dicetak.');
                return;
            }

            const payload = {
                items: this.items.map(i => ({ menu_id: i.menu_id, qty: i.qty })),
                tipe_pesanan: this.tipePesanan,
                nomor_meja: this.nomorMeja,
                _token: '{{ csrf_token() }}',
            };

            fetch('{{ route("kasir.pesanan.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload),
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) { toast('error', data.message || 'Gagal membuat pesanan.'); return; }

                const transaksiId = data.data.id;
                fetch(`/kasir/pesanan/${transaksiId}/bayar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        bayar: this.bayarValue,
                        tipe_bayar: this.tipeBayar,
                    }),
                })
                .then(res => res.json())
                .then(bayarData => {
                    if (!bayarData.success) { toast('error', bayarData.message || 'Gagal memproses pembayaran.'); return; }
                    this.strukPelanggan = bayarData.data.struk_pelanggan;
                    this.strukDapur = bayarData.data.struk_dapur;
                    this.showPaymentModal = false;
                    this.showStrukModal = true;
                })
                .catch(() => toast('error', 'Terjadi kesalahan saat pembayaran.'));
            })
            .catch(() => toast('error', 'Terjadi kesalahan saat membuat pesanan.'));
        },

        bayarPending(pt) {
            this.pendingTarget = pt;
            this.tipeBayar = 'tunai';
            this.bayarDisplay = '';
            this.bayarValue = 0;
            this.showBayarPendingModal = true;
        },

        konfirmasiBayarPending() {
            const pt = this.pendingTarget;
            if (!pt) return;
            if (this.bayarValue < pt.total_harga) {
                toast('warning', 'Nominal bayar kurang dari total harga.');
                return;
            }

            fetch(`/kasir/pesanan/${pt.id}/bayar-pending`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    bayar: this.bayarValue,
                    tipe_bayar: this.tipeBayar,
                }),
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) { toast('error', data.message || 'Gagal.'); return; }
                this.strukPelanggan = data.data.struk_pelanggan;
                this.showBayarPendingModal = false;
                this.pendingTarget = null;
                this.pendingTransaksis = this.pendingTransaksis.filter(t => t.id !== pt.id);
                this.cetakPelanggan = true;
                this.cetakDapur = false;
                this.showStrukModal = true;
                toast('success', 'Pembayaran berhasil.');
            })
            .catch(() => toast('error', 'Terjadi kesalahan.'));
        },

        cetakStruk() {
            const printWindow = window.open('', '_blank');
            let content = '';

            if (this.cetakPelanggan) {
                content += this.buildStrukHTML('pelanggan');
            }

            if (this.cetakPelanggan && this.cetakDapur) {
                content += `
                    <div style="text-align:center; margin:12px 0;">
                        <div style="border-top:2px dashed #000; margin-bottom:2px;"></div>
                        <p style="font-size:10px; font-weight:bold; letter-spacing:3px;">- - - - - - - - - - POTONG DISINI - - - - - - - - - -</p>
                        <div style="border-top:2px dashed #000; margin-top:2px;"></div>
                    </div>
                `;
            }

            if (this.cetakDapur) {
                content += this.buildStrukHTML('dapur');
            }

            printWindow.document.write(`
                <html><head><title>Struk Pembayaran</title>
                <style>
                    body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 10px; }
                    .text-center { text-align: center; }
                    .font-bold { font-weight: bold; }
                    @media print { body { margin: 0; padding: 10px; } }
                </style></head><body>
            `);
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => { printWindow.print(); }, 500);
        },

        buildStrukHTML(type) {
            const data = type === 'pelanggan' ? this.strukPelanggan : this.strukDapur;
            let html = '';

            if (type === 'pelanggan') {
                html = `
                    <div style="text-align:center; border-bottom:1px dashed #000; padding-bottom:8px; margin-bottom:8px;">
                        <h2 style="font-size:14px;font-weight:bold;">${data.nama_usaha}</h2>
                        <p style="font-size:10px;">${data.alamat || ''}</p>
                        <p style="font-size:10px;">${data.kontak || ''}</p>
                    </div>
                    <table style="width:100%;font-size:10px;margin-bottom:6px;">
                        <tr><td style="width:40%;">No. Pesanan</td><td style="text-align:right;font-weight:bold;">${data.nomor_pesanan}</td></tr>
                        <tr><td>Tipe</td><td style="text-align:right;">${data.tipe_pesanan}</td></tr>
                        ${data.nomor_meja ? `<tr><td>Meja</td><td style="text-align:right;">Meja ${data.nomor_meja}</td></tr>` : ''}
                        <tr><td>Tanggal</td><td style="text-align:right;">${data.datetime}</td></tr>
                    </table>
                    <div style="border-top:1px dashed #000;border-bottom:1px dashed #000;padding:4px 0;margin-bottom:6px;">
                        ${data.items.map(item => `
                            <div style="display:flex;justify-content:space-between;font-size:10px;padding:1px 0;">
                                <span style="flex:1;">${item.nama}</span>
                                <span style="margin:0 8px;color:#666;">${item.qty}x</span>
                                <span style="font-weight:bold;">${rupiah(item.subtotal)}</span>
                            </div>
                        `).join('')}
                    </div>
                    <div style="font-size:10px;margin-bottom:6px;">
                        <div style="display:flex;justify-content:space-between;font-weight:bold;border-top:1px dashed #000;border-bottom:1px dashed #000;padding:3px 0;margin:3px 0;">
                            <span>TOTAL</span><span>${rupiah(data.total_harga)}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;color:#666;padding:1px 0;">
                            <span>Bayar</span><span>${data.bayar !== null ? rupiah(data.bayar) : '-'}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;color:#047857;font-weight:bold;padding:1px 0;">
                            <span>Kembalian</span><span>${data.kembalian !== null ? rupiah(data.kembalian) : '-'}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;color:#666;padding:1px 0;">
                            <span>Tipe Bayar</span><span style="text-transform:capitalize;">${data.tipe_bayar || 'Pending'}</span>
                        </div>
                    </div>
                    <div style="text-align:center;font-size:9px;border-top:1px dashed #000;padding-top:6px;color:#666;">
                        <p>Kasir: ${data.kasir}</p>
                        <p>Terima kasih telah berbelanja</p>
                    </div>
                `;
            } else {
                html = `
                    <div style="text-align:center; border-bottom:1px dashed #000; padding-bottom:8px; margin-bottom:8px;">
                        <h2 style="font-size:14px;font-weight:bold;">${data.nama_usaha}</h2>
                        <p style="font-size:10px;font-weight:bold;margin-top:2px;">STRUK DAPUR / KITCHEN ORDER</p>
                    </div>
                    <table style="width:100%;font-size:10px;margin-bottom:6px;">
                        <tr><td style="width:40%;">No. Pesanan</td><td style="text-align:right;font-weight:bold;">${data.nomor_pesanan}</td></tr>
                        <tr><td>Tipe</td><td style="text-align:right;">${data.tipe_pesanan}</td></tr>
                        ${data.nomor_meja ? `<tr><td>Meja</td><td style="text-align:right;">Meja ${data.nomor_meja}</td></tr>` : ''}
                        <tr><td>Tanggal</td><td style="text-align:right;">${data.datetime}</td></tr>
                    </table>
                    <div style="border-top:1px dashed #000;border-bottom:1px dashed #000;padding:6px 0;margin-bottom:6px;">
                        ${data.items.map(item => `
                            <div style="display:flex;justify-content:space-between;font-size:11px;padding:3px 0;">
                                <span style="flex:1;font-weight:bold;">${item.nama}</span>
                                <span style="font-weight:bold;color:#064E3B;">${item.qty}x</span>
                            </div>
                        `).join('')}
                    </div>
                    <div style="text-align:center;font-size:9px;border-top:1px dashed #000;padding-top:6px;color:#666;">
                        <p>Terima kasih</p>
                        <p>Selamat Menikmati</p>
                    </div>
                `;
            }
            return html;
        },

        resetPesanan() {
            this.items = [];
            this.nomorMeja = '';
            this.strukPelanggan = {};
            this.strukDapur = {};
        },
    };
}
</script>
@endpush
