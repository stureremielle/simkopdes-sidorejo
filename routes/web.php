<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/layanan', [HomeController::class, 'layanan'])->name('layanan');
Route::get('/berita', [HomeController::class, 'berita'])->name('berita');
Route::get('/berita/{id}', [HomeController::class, 'detailBerita'])->name('berita.detail');
Route::get('/pengumuman/{id}', [HomeController::class, 'detailPengumuman'])->name('pengumuman.detail');
Route::get('/galeri', [HomeController::class, 'galeri'])->name('galeri');
Route::get('/tentang-kami', [HomeController::class, 'tentang'])->name('tentang');
Route::get('/kontak', [HomeController::class, 'kontak'])->name('kontak');
Route::get('/daftar', [HomeController::class, 'daftar'])->name('daftar');
Route::post('/daftar', [HomeController::class, 'prosesDaftar'])->name('daftar.store');

// Admin Guest Routes (Login)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Admin Protected Routes (Guarded by auth:admin)
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/anggota/verifikasi', [AdminController::class, 'verifyAnggota'])->name('anggota.verifikasi');

    // CRUD Pages
    Route::get('/layanan', [\App\Http\Controllers\Admin\LayananController::class, 'index'])->name('layanan');
    Route::post('/layanan', [\App\Http\Controllers\Admin\LayananController::class, 'store'])->name('layanan.store');
    Route::post('/layanan/kategori', [\App\Http\Controllers\Admin\LayananController::class, 'storeCategory'])->name('kategori.store');
    Route::delete('/layanan/kategori/{kategori}', [\App\Http\Controllers\Admin\LayananController::class, 'destroyCategory'])->name('kategori.destroy');
    Route::post('/layanan/toggle/{id}', [\App\Http\Controllers\Admin\LayananController::class, 'toggleStatus'])->name('layanan.toggle');
    Route::put('/layanan/{id}', [\App\Http\Controllers\Admin\LayananController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/{id}', [\App\Http\Controllers\Admin\LayananController::class, 'destroy'])->name('layanan.destroy');

    Route::get('/berita', [\App\Http\Controllers\Admin\BeritaController::class, 'index'])->name('berita');
    Route::post('/berita/kategori', [\App\Http\Controllers\Admin\BeritaController::class, 'storeCategory'])->name('berita.kategori.store');
    Route::delete('/berita/kategori/{kategori}', [\App\Http\Controllers\Admin\BeritaController::class, 'destroyCategory'])->name('berita.kategori.destroy');
    Route::post('/berita', [\App\Http\Controllers\Admin\BeritaController::class, 'store'])->name('berita.store');
    Route::put('/berita/{id}', [\App\Http\Controllers\Admin\BeritaController::class, 'update'])->name('berita.update');
    Route::delete('/berita/{id}', [\App\Http\Controllers\Admin\BeritaController::class, 'destroy'])->name('berita.destroy');

    Route::get('/galeri', [\App\Http\Controllers\Admin\GaleriController::class, 'index'])->name('galeri');
    Route::post('/galeri/kategori', [\App\Http\Controllers\Admin\GaleriController::class, 'storeCategory'])->name('galeri.kategori.store');
    Route::delete('/galeri/kategori/{kategori}', [\App\Http\Controllers\Admin\GaleriController::class, 'destroyCategory'])->name('galeri.kategori.destroy');
    Route::post('/galeri', [\App\Http\Controllers\Admin\GaleriController::class, 'store'])->name('galeri.store');
    Route::put('/galeri/{id}', [\App\Http\Controllers\Admin\GaleriController::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/{id}', [\App\Http\Controllers\Admin\GaleriController::class, 'destroy'])->name('galeri.destroy');

    Route::get('/anggota', [AdminController::class, 'anggota'])->name('anggota');
    Route::get('/data-anggota', [AdminController::class, 'anggota'])->name('data-anggota');
    Route::post('/anggota', [AdminController::class, 'simpanAnggota'])->name('anggota.store');
    Route::put('/anggota/{id}', [AdminController::class, 'updateAnggota'])->name('anggota.update');
    Route::delete('/anggota/{id}', [AdminController::class, 'hapusAnggota'])->name('anggota.delete');

    Route::post('/layanan/featured', [\App\Http\Controllers\Admin\LayananController::class, 'saveFeatured'])->name('layanan.featured');

    Route::get('/penyimpanan', [\App\Http\Controllers\Admin\PenyimpananController::class, 'index'])->name('penyimpanan');
    Route::post('/penyimpanan/kategori', [\App\Http\Controllers\Admin\PenyimpananController::class, 'storeCategory'])->name('penyimpanan.kategori.store');
    Route::delete('/penyimpanan/kategori/{kategori}', [\App\Http\Controllers\Admin\PenyimpananController::class, 'destroyCategory'])->name('penyimpanan.kategori.destroy');
    Route::post('/penyimpanan', [\App\Http\Controllers\Admin\PenyimpananController::class, 'upload'])->name('penyimpanan.upload');
    Route::put('/penyimpanan/{id}', [\App\Http\Controllers\Admin\PenyimpananController::class, 'update'])->name('penyimpanan.update');
    Route::delete('/penyimpanan/{id}', [\App\Http\Controllers\Admin\PenyimpananController::class, 'destroy'])->name('penyimpanan.destroy');

    Route::resource('pengumuman', \App\Http\Controllers\Admin\PengumumanController::class);

    Route::get('/pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'index'])->name('pengaturan');
    Route::post('/pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'save'])->name('pengaturan.save');
    Route::post('/pengaturan/hero', [\App\Http\Controllers\Admin\PengaturanController::class, 'saveHeroBg'])->name('pengaturan.hero');
    Route::post('/pengaturan/org-chart', [\App\Http\Controllers\Admin\PengaturanController::class, 'saveOrgChart'])->name('pengaturan.org_chart');
    Route::post('/pengaturan/ubah-password', [\App\Http\Controllers\Admin\PengaturanController::class, 'changePassword'])->name('pengaturan.password');
});

// Backward compatibility or alternative logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
