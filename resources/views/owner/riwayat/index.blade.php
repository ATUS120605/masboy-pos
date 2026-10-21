@extends('owner.layout')

@section('title', 'Riwayat Transaksi')

@section('header', 'Riwayat Transaksi')

@section('content')
<div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
    <form method="GET" action="{{ route('owner.riwayat') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pesanan..." class="pl-10 pr-4 py-2 w-full sm:w-48 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
        </div>
        <select name="kasir_id" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
            <option value="">Semua Kasir</option>
            @foreach ($kasirList as $kasir)
                <option value="{{ $kasir->id }}" {{ request('kasir_id') == $kasir->id ? 'selected' : '' }}>{{ $kasir->name }}</option>
            @endforeach
        </select>
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
        <button type="submit" class="px-6 py-2.5 bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-medium rounded-xl transition">Filter</button>
        @if (request('search') || request('start_date') || request('end_date') || request('kasir_id'))
            <a href="{{ route('owner.riwayat') }}" class="inline-flex items-center px-3 py-2 text-sm text-emerald-900 bg-white border border-emerald-900 hover:bg-emerald-50 rounded-xl transition">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No Transaksi</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kasir</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu</th>
                <th class="text-center px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($transaksis as $transaksi)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 text-sm font-medium text-slate-900">
                        {{ $transaksi->pesanans->first()->nomor_pesanan ?? '#' . $transaksi->id }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $transaksi->kasir->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $transaksi->pesanans->first()->tipe_pesanan ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-right font-medium text-slate-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $transaksi->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('owner.riwayat.show', $transaksi->id) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-medium rounded-xl transition mr-1">
                            Lihat
                        </a>
                        <a href="{{ route('owner.riwayat.struk', $transaksi->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-medium rounded-xl transition mr-1">
                            Cetak Struk
                        </a>
                        <form action="{{ route('owner.riwayat.destroy', $transaksi->id) }}" method="POST" class="inline" onsubmit="confirmHapus(event, 'Yakin ingin menghapus transaksi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-xl transition">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm">Belum ada transaksi.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($transaksis->hasPages())
    <div class="mt-4">
        {{ $transaksis->links() }}
    </div>
@endif
@endsection
