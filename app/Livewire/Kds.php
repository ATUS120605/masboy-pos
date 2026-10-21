<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pesanan;

class Kds extends Component
{
    public $pesananDimasak = [];
    public $pesananSelesai = [];
    public $waktuSekarang;

    public function mount()
    {
        $this->refreshPesanan();
    }

    public function refreshPesanan()
    {
        $this->waktuSekarang = now();
        $this->pesananDimasak = Pesanan::with(['detailPesanans.menu', 'transaksi.kasir'])
            ->where('status', 'dimasak')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();

        $this->pesananSelesai = Pesanan::with(['detailPesanans.menu', 'transaksi.kasir'])
            ->where('status', 'selesai')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
    }

    public function selesaikan($pesananId)
    {
        $pesanan = Pesanan::findOrFail($pesananId);
        $pesanan->status = 'selesai';
        $pesanan->save();
        $this->refreshPesanan();
        $this->dispatch('toast', icon: 'success', title: 'Pesanan #' . $pesanan->nomor_pesanan . ' selesai');
    }

    public function render()
    {
        return view('livewire.kds');
    }
}
