<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with('kategori');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function ($q) use ($search) {
                      $q->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $menus = $query->latest()->paginate(10);
        $kategoris = Kategori::all();

        return view('owner.menu.index', compact('menus', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        return view('owner.menu.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('menu', 'public');
        }

        Menu::create($validated);

        return redirect()->route('owner.menu.index', ['page' => $request->page])->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        $kategoris = Kategori::all();
        return view('owner.menu.index', compact('menu', 'kategoris'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($menu->foto) {
                Storage::disk('public')->delete($menu->foto);
            }
            $validated['foto'] = $request->file('foto')->store('menu', 'public');
        }

        $menu->update($validated);

        return redirect()->route('owner.menu.index', ['page' => $request->page])->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->foto) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        return redirect()->route('owner.menu.index', ['page' => $request->page])->with('success', 'Menu berhasil dihapus.');
    }
}
