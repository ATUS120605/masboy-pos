<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaksi;

class KasirRiwayat extends Component
{
    public $search = '';

    public function render()
    {
        $query = Transaksi::with(['kasir', 'pesanans'])
            ->whereDate('created_at', today());

        if (trim($this->search)) {
            $query->whereHas('pesanans', function ($q) {
                $q->where('nomor_pesanan', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.kasir-riwayat', [
            'transaksis' => $query->latest()->take(50)->get()->toArray(),
        ]);
    }
}
