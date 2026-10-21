<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::firstOrNew([], [
            'nama_usaha' => 'MasBoy POS',
            'alamat' => '',
            'kontak' => '',
        ]);

        return view('owner.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:255',
        ]);

        $pengaturan = Pengaturan::first();

        if ($pengaturan) {
            $pengaturan->update($validated);
        } else {
            Pengaturan::create($validated);
        }

        return redirect()->route('owner.pengaturan')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
