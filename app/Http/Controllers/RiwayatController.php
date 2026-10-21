<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['kasir', 'pesanans']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pesanans', function ($q) use ($search) {
                $q->where('nomor_pesanan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kasir_id')) {
            $query->where('kasir_id', $request->kasir_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transaksis = $query->latest()->paginate(10);
        $kasirList = User::role('kasir')->orderBy('name')->get();

        return view('owner.riwayat.index', compact('transaksis', 'kasirList'));
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['kasir', 'pesanans.detailPesanans.menu'])->findOrFail($id);
        return view('owner.riwayat.show', compact('transaksi'));
    }

    public function printStruk($id)
    {
        $transaksi = Transaksi::with(['kasir', 'pesanans.detailPesanans.menu'])->findOrFail($id);
        $pengaturan = Pengaturan::first();
        return view('struk.print', compact('transaksi', 'pengaturan'));
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();
        return redirect()->route('owner.riwayat')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function kasirIndex()
    {
        return view('kasir.riwayat');
    }
}
