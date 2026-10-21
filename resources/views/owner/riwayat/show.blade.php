@extends('owner.layout')

@section('title', 'Detail Transaksi')

@section('header', 'Detail Transaksi')

@section('content')
<div class="mb-4">
    <a href="{{ route('owner.riwayat') }}" class="inline-flex items-center text-sm text-slate-600 hover:text-slate-900 transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl mx-auto">
    <div class="text-center border-b border-slate-200 pb-4 mb-4">
        <h2 class="text-lg font-bold text-gray-900">Struk Pembayaran</h2>
        <p class="text-sm text-slate-500">{{ config('app.name', 'MasBoy POS') }}</p>
    </div>

    <div class="space-y-2 text-sm mb-4">
        <div class="flex justify-between">
            <span class="text-slate-500">No. Pesanan</span>
            <span class="font-medium">{{ $transaksi->pesanans->first()->nomor_pesanan ?? '#' . $transaksi->id }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500">Tipe</span>
            <span>{{ $transaksi->pesanans->first()->tipe_pesanan ?? '-' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500">Meja</span>
            <span>{{ $transaksi->pesanans->first()->nomor_meja ?? '-' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500">Tanggal</span>
            <span>{{ $transaksi->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500">Kasir</span>
            <span>{{ $transaksi->kasir->name ?? '-' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500">Tipe Bayar</span>
            <span>{{ $transaksi->tipe_bayar ? ucfirst($transaksi->tipe_bayar) : 'Pending' }}</span>
        </div>
    </div>

    <div class="border-t border-slate-200 pt-4 mb-4">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="text-left py-2 text-slate-500 font-medium">Menu</th>
                    <th class="text-center py-2 text-slate-500 font-medium">Qty</th>
                    <th class="text-right py-2 text-slate-500 font-medium">Harga</th>
                    <th class="text-right py-2 text-slate-500 font-medium">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($transaksi->pesanans as $pesanan)
                    @foreach ($pesanan->detailPesanans as $detail)
                        <tr>
                            <td class="py-2">{{ $detail->menu->nama_menu ?? 'Menu#' . $detail->menu_id }}</td>
                            <td class="py-2 text-center">{{ $detail->qty }}</td>
                            <td class="py-2 text-right">Rp {{ number_format($detail->menu->harga ?? 0, 0, ',', '.') }}</td>
                            <td class="py-2 text-right font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-200 pt-4 space-y-2 text-sm">
        <div class="flex justify-between font-bold text-base">
            <span>Total</span>
            <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Bayar</span>
            <span>{{ $transaksi->bayar !== null ? 'Rp ' . number_format($transaksi->bayar, 0, ',', '.') : '-' }}</span>
        </div>
        <div class="flex justify-between">
            <span>Kembalian</span>
            <span>{{ $transaksi->kembalian !== null ? 'Rp ' . number_format($transaksi->kembalian, 0, ',', '.') : '-' }}</span>
        </div>
    </div>

    <div class="text-center mt-6">
        <a href="{{ route('owner.riwayat.struk', $transaksi->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-medium rounded-xl transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Struk
        </a>
    </div>
</div>
@endsection
