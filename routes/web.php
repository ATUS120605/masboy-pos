<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole('owner')) {
            return redirect('/owner/dashboard');
        }
        // Arahkan otomatis ke dashboard saat kasir login
        return redirect('/kasir/dashboard');
    }
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/profile', [AuthController::class, 'showEditProfile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile']);
});

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'ownerDashboard'])->name('dashboard');

    Route::resource('kategori', KategoriController::class)->except(['show']);
    Route::resource('menu', MenuController::class)->except(['show']);

    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('index');
        Route::post('/', [KasirController::class, 'store'])->name('store');
        Route::put('/{user}', [KasirController::class, 'update'])->name('update');
        Route::delete('/{user}', [KasirController::class, 'destroy'])->name('destroy');
    });

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('riwayat.show');
    Route::delete('/riwayat/{id}', [RiwayatController::class, 'destroy'])->name('riwayat.destroy');
    Route::get('/riwayat/{id}/struk', [RiwayatController::class, 'printStruk'])->name('riwayat.struk');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
});

Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->group(function () {
    // INI DIA RUTE YANG KETINGGALAN TADI:
    Route::get('/dashboard', [DashboardController::class, 'kasirDashboard'])->name('kasir.dashboard');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('kasir.pesanan');
    Route::get('/menu-stok', [PesananController::class, 'menuStok'])->name('kasir.menu-stok');
    Route::get('/pesanan/pending-data', [PesananController::class, 'pendingData'])->name('kasir.pesanan.pending-data');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('kasir.pesanan.store');
    Route::post('/pesanan/{id}/bayar', [PesananController::class, 'bayar'])->name('kasir.pesanan.bayar');
    Route::post('/pesanan/bayar-nanti', [PesananController::class, 'bayarNanti'])->name('kasir.pesanan.bayar-nanti');
    Route::post('/pesanan/{id}/bayar-pending', [PesananController::class, 'bayarPending'])->name('kasir.pesanan.bayar-pending');
    Route::post('/pesanan/{id}/struk', [PesananController::class, 'cetakStruk'])->name('kasir.pesanan.struk');

    Route::get('/kds', function () {
        return view('kasir.kds');
    })->name('kasir.kds');

    Route::get('/riwayat', [RiwayatController::class, 'kasirIndex'])->name('kasir.riwayat');
    Route::get('/riwayat/{id}/struk', [RiwayatController::class, 'printStruk'])->name('kasir.riwayat.struk');
});