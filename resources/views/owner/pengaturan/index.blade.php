@extends('owner.layout')

@section('title', 'Pengaturan')

@section('header', 'Pengaturan')

@section('content')
@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-xl text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl">
    <form method="POST" action="{{ route('owner.pengaturan.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nama_usaha" class="block text-sm font-medium text-slate-700 mb-1">Nama Usaha</label>
            <input type="text" name="nama_usaha" id="nama_usaha" value="{{ old('nama_usaha', $pengaturan->nama_usaha) }}" required
                class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                placeholder="Masukkan nama usaha">
        </div>

        <div class="mb-4">
            <label for="alamat" class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
            <textarea name="alamat" id="alamat" rows="3" required
                class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                placeholder="Masukkan alamat">{{ old('alamat', $pengaturan->alamat) }}</textarea>
        </div>

        <div class="mb-4">
            <label for="kontak" class="block text-sm font-medium text-slate-700 mb-1">Kontak (Telepon/HP)</label>
            <input type="text" name="kontak" id="kontak" value="{{ old('kontak', $pengaturan->kontak) }}" required
                class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                placeholder="Masukkan nomor kontak">
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-900 hover:bg-emerald-800 rounded-xl transition">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
