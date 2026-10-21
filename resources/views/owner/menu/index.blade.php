@extends('owner.layout')

@section('title', 'Kelola Menu')

@section('header', 'Kelola Menu')

@section('content')
<div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
    <form method="GET" action="{{ route('owner.menu.index') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kategori..." class="pl-10 pr-4 py-2 w-full sm:w-64 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
        </div>
        <select name="kategori_id" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
            <option value="">Semua Kategori</option>
            @foreach ($kategoris as $kat)
                <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
            @endforeach
        </select>
        @if (request('search') || request('kategori_id'))
            <a href="{{ route('owner.menu.index') }}" class="inline-flex items-center px-3 py-2 text-sm text-emerald-900 bg-white border border-emerald-900 hover:bg-emerald-50 rounded-xl transition">Reset</a>
        @endif
    </form>
    <button onclick="openModal()" class="inline-flex items-center px-6 py-2.5 bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-medium rounded-xl transition shrink-0">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Menu
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Foto</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Harga</th>
                <th class="text-center px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok</th>
                <th class="text-center px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($menus as $menu)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        @if ($menu->foto)
                            <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-10 h-10 rounded-lg object-cover">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $menu->nama_menu }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $menu->kategori->nama_kategori }}</td>
                    <td class="px-6 py-4 text-sm text-right font-medium text-slate-900">Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $stok = $menu->stok;
                            $badgeClass = $stok > 10 ? 'bg-emerald-100 text-emerald-900' : ($stok > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            $badgeLabel = $stok > 10 ? 'Tersedia' : ($stok > 0 ? 'Kritis' : 'Habis');
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                            {{ $stok }} ({{ $badgeLabel }})
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="openModal({{ $menu->id }}, '{{ $menu->nama_menu }}', {{ $menu->kategori_id }}, {{ $menu->harga }}, {{ $menu->stok }})" class="inline-flex items-center px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium rounded-xl transition mr-1">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                        <form action="{{ route('owner.menu.destroy', $menu) }}" method="POST" class="inline" onsubmit="confirmHapus(event, 'Yakin ingin menghapus menu ini?')">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <p class="text-sm">Belum ada menu.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($menus->hasPages())
    <div class="mt-4">
        {{ $menus->links() }}
    </div>
@endif

{{-- Modal --}}
<div id="menuModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold tracking-tight text-slate-900" id="modalTitle">Tambah Menu</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="menuForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>
                <input type="hidden" name="page" id="pageInput" value="{{ request('page', 1) }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="nama_menu" class="block text-sm font-medium text-gray-700 mb-1">Nama Menu</label>
                        <input type="text" name="nama_menu" id="nama_menu" required
                            class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                            placeholder="Masukkan nama menu">
                    </div>
                    <div>
                        <label for="kategori_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="kategori_id" id="kategori_id" required
                            class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition">
                            <option value="">Pilih Kategori</option>
                            @foreach ($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="harga" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                        <input type="text" name="harga" id="harga" required
                            class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                            placeholder="0">
                    </div>
                    <div>
                        <label for="stok" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                        <input type="number" name="stok" id="stok" required min="0"
                            class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                            placeholder="0">
                    </div>
                    <div>
                        <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto (opsional)</label>
                        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100 transition">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2.5 text-sm font-medium text-emerald-900 bg-white border border-emerald-900 hover:bg-emerald-50 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-900 hover:bg-emerald-800 rounded-xl transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal(id = null, nama = '', kategoriId = '', harga = '', stok = '') {
    const modal = document.getElementById('menuModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('menuForm');
    const methodField = document.getElementById('methodField');

    const params = new URLSearchParams(window.location.search);
    const page = params.get('page') || '1';
    document.getElementById('pageInput').value = page;

    if (id) {
        title.textContent = 'Edit Menu';
        form.action = '{{ url("owner/menu") }}/' + id;
        methodField.innerHTML = '@method("PUT")';
        document.getElementById('nama_menu').value = nama;
        document.getElementById('kategori_id').value = kategoriId;
        document.getElementById('harga').value = harga;
        document.getElementById('stok').value = stok;
    } else {
        title.textContent = 'Tambah Menu';
        form.action = '{{ route("owner.menu.store") }}';
        methodField.innerHTML = '';
        document.getElementById('nama_menu').value = '';
        document.getElementById('kategori_id').value = '';
        document.getElementById('harga').value = '';
        document.getElementById('stok').value = '';
        document.getElementById('foto').value = '';
    }

    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('menuModal').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>
@endpush
@endsection
