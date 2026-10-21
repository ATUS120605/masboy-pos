@extends('layouts.app') 

@section('content')
<div class="p-6 min-h-screen">
    
    <!-- 4 Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Card 1: Tunai -->
        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center gap-4">
            <!-- Pakai style inline biar warnanya PASTI muncul -->
            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #dcfce7; color: #16a34a;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tunai</p>
                <h3 class="text-xl font-extrabold text-gray-800">
                    @php 
                        $tunai = $rincianMetode->where('tipe_bayar', 'tunai')->first();
                    @endphp
                    Rp {{ $tunai ? number_format($tunai->total, 0, ',', '.') : '0' }}
                </h3>
            </div>
        </div>

        <!-- Card 2: Qris -->
        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #cffafe; color: #06b6d4;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Qris</p>
                <h3 class="text-xl font-extrabold text-gray-800">
                    @php 
                        $qris = $rincianMetode->where('tipe_bayar', 'qris')->first();
                    @endphp
                    Rp {{ $qris ? number_format($qris->total, 0, ',', '.') : '0' }}
                </h3>
            </div>
        </div>

        <!-- Card 3: Total Pendapatan -->
        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center gap-4">
            <!-- Diubah ke Hitam/Gelap & Pakai icon coin mata uang ($) di dalam lingkaran -->
            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #2D2D2D; color: #ffffff;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Pendapatan</p>
                <h3 class="text-xl font-extrabold text-gray-800">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <!-- Card 4: Total Transaksi -->
        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #fae8ff; color: #d946ef;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Transaksi</p>
                <h3 class="text-xl font-extrabold text-gray-800">
                    {{ $totalTransaksi }}
                </h3>
            </div>
        </div>

    </div>

    <!-- Table Section (Tanpa Garis Sedikitpun!) -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 pb-2">
            <h2 class="text-sm font-bold text-gray-800">Rincian Metode Pembayaran</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-center">
                <thead class="text-gray-400 text-xs font-bold uppercase">
                    <tr>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4">Jumlah Transaksi</th>
                        <th class="px-6 py-4">Total</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-800 font-medium border-none">
                    
                    @if(isset($rincianMetode) && $rincianMetode->count() > 0)
                        @foreach($rincianMetode as $metode)
                        <tr class="border-none">
                            <td class="px-6 py-4 capitalize">{{ $metode->tipe_bayar }}</td>
                            <td class="px-6 py-4">{{ $metode->jumlah_transaksi }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($metode->total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="border-none">
                            <td colspan="3" class="px-6 py-8 text-gray-400 text-center">
                                Belum ada transaksi pada shift ini.
                            </td>
                        </tr>
                    @endif
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection