<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function ownerDashboard()
    {
        return view('owner.dashboard');
    }

    public function kasirDashboard()
    {
        $now = Carbon::now();
        
        // Logika Batas Siklus Operasional Shift (07.00 - 06.59 WITA)
        if ($now->hour >= 7) {
            $startWaktu = $now->copy()->setTime(7, 0, 0);
        } else {
            $startWaktu = $now->copy()->subDay()->setTime(7, 0, 0);
        }
        $endWaktu = $startWaktu->copy()->addDay()->subSecond();

        // Mengambil ID kasir yang sedang aktif login
        $kasirId = Auth::id();

        // Total setoran khusus kasir ini pada shift ini
        $totalPendapatan = Transaksi::where('kasir_id', $kasirId) 
            ->whereBetween('created_at', [$startWaktu, $endWaktu])
            ->sum('total_harga');

        // Total jumlah transaksi kasir ini
        $totalTransaksi = Transaksi::where('kasir_id', $kasirId)
            ->whereBetween('created_at', [$startWaktu, $endWaktu])
            ->count();

        // Rincian metode pembayaran (Tunai vs QRIS) khusus kasir ini
        $rincianMetode = Transaksi::where('kasir_id', $kasirId)
            ->whereBetween('created_at', [$startWaktu, $endWaktu])
            ->selectRaw('tipe_bayar, count(*) as jumlah_transaksi, sum(total_harga) as total')
            ->groupBy('tipe_bayar')
            ->get();

        return view('kasir.dashboard', compact(
            'totalPendapatan', 
            'totalTransaksi', 
            'rincianMetode'
        ));
    }
}