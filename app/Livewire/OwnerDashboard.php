<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Transaksi;

class OwnerDashboard extends Component
{
    public function render()
    {
        $hariIni = now()->toDateString();
        $bulanIni = now()->startOfMonth();

        return view('livewire.owner-dashboard', [
            'pendapatanHariIni' => Transaksi::whereDate('created_at', $hariIni)->sum('total_harga'),
            'pendapatanBulanIni' => Transaksi::where('created_at', '>=', $bulanIni)->sum('total_harga'),
            'totalTransaksi' => Pesanan::where('status', 'selesai')->whereDate('updated_at', $hariIni)->count(),
            'stokKritis' => Menu::where('stok', '<=', 5)->with('kategori')->get()->toArray(),
            'rekomendasiStok' => array_map(function ($item) {
                $item['rekomendasi'] = $item['total_qty'] * 2;
                return $item;
            }, DetailPesanan::selectRaw('menu_id, SUM(qty) as total_qty')
                ->whereHas('pesanan', function ($q) use ($bulanIni) {
                    $q->where('created_at', '>=', $bulanIni);
                })
                ->groupBy('menu_id')
                ->orderByDesc('total_qty')
                ->with('menu')
                ->take(5)
                ->get()
                ->toArray()),
        ]);
    }
}
