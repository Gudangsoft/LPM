<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\HalamanController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\KategoriBeritaController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\DokumenController as AdminDokumenController;
use App\Http\Controllers\Admin\JenisDokumenController;
use App\Http\Controllers\Admin\StrukturOrganisasiController;
use App\Http\Controllers\Admin\HalamanController as AdminHalamanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\PengumumanController as AdminPengumumanController;
use App\Http\Controllers\Admin\AgendaController as AdminAgendaController;
use App\Http\Controllers\Admin\KontakController as AdminKontakController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\DatabaseController;
use App\Http\Controllers\Admin\ProdiController;
use App\Http\Controllers\Admin\AkreditasiController;
use App\Http\Controllers\Admin\PeriodeAmiController;
use App\Http\Controllers\Admin\AuditorController;
use App\Http\Controllers\Admin\JadwalAmiController;
use App\Http\Controllers\Admin\TemuanAmiController;
use App\Http\Controllers\Admin\TindakLanjutController;
use App\Http\Controllers\Admin\StatistikController;

/*
|--------------------------------------------------------------------------
| Language Switcher
|--------------------------------------------------------------------------
*/
Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('language.switch');

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// Profil LPM
Route::get('/profil', [HalamanController::class, 'profil'])->name('profil');
Route::get('/visi-misi', [HalamanController::class, 'visiMisi'])->name('visi-misi');
Route::get('/struktur-organisasi', [HalamanController::class, 'strukturOrganisasi'])->name('struktur-organisasi');

// Sistem Penjaminan Mutu
Route::get('/sistem-penjaminan-mutu', [HalamanController::class, 'sistemPenjaminanMutu'])->name('sistem-penjaminan-mutu');
Route::get('/audit-mutu-internal', [HalamanController::class, 'auditMutuInternal'])->name('audit-mutu-internal');
Route::get('/akreditasi', [HalamanController::class, 'akreditasi'])->name('akreditasi');

// Berita
Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{slug}', [BeritaController::class, 'show'])->name('berita.show');
Route::get('/berita/kategori/{slug}', [BeritaController::class, 'kategori'])->name('berita.kategori');

// Galeri
Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri.index');
Route::get('/galeri/{slug}', [GaleriController::class, 'show'])->name('galeri.show');

// Dokumen
Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
Route::get('/dokumen/download/{slug}', [DokumenController::class, 'download'])->name('dokumen.download');

// Pengumuman
Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
Route::get('/pengumuman/{slug}', [PengumumanController::class, 'show'])->name('pengumuman.show');

// Agenda
Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
Route::get('/agenda/{slug}', [AgendaController::class, 'show'])->name('agenda.show');

// Kontak
Route::get('/kontak', [KontakController::class, 'index'])->name('kontak.index');
Route::post('/kontak', [KontakController::class, 'store'])->name('kontak.store');

// Halaman Dinamis
Route::get('/halaman/{slug}', [HalamanController::class, 'show'])->name('halaman.show');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Berita
    Route::resource('berita', AdminBeritaController::class)->except(['show'])->parameters(['berita' => 'berita']);
    
    // Kategori Berita
    Route::resource('kategori-berita', KategoriBeritaController::class)->except(['show'])->parameters(['kategori-berita' => 'kategori']);
    
    // Galeri
    Route::resource('galeri', AdminGaleriController::class)->except(['show'])->parameters(['galeri' => 'galeri']);
    
    // Dokumen
    Route::resource('dokumen', AdminDokumenController::class)->except(['show'])->parameters(['dokumen' => 'dokumen']);
    
    // Jenis Dokumen
    Route::resource('jenis-dokumen', JenisDokumenController::class)->except(['show'])->parameters(['jenis-dokumen' => 'jenisDokuman']);
    
    // Struktur Organisasi
    Route::resource('struktur-organisasi', StrukturOrganisasiController::class)->except(['show'])->parameters(['struktur-organisasi' => 'struktur']);
    
    // Halaman
    Route::resource('halaman', AdminHalamanController::class)->except(['show'])->parameters(['halaman' => 'halaman']);
    
    // Users
    Route::resource('users', UserController::class)->except(['show']);
    
    // Slider
    Route::resource('sliders', SliderController::class)->except(['show']);
    
    // Pengumuman
    Route::resource('pengumuman', AdminPengumumanController::class)->except(['show'])->parameters(['pengumuman' => 'pengumuman']);
    
    // Agenda
    Route::resource('agenda', AdminAgendaController::class)->except(['show'])->parameters(['agenda' => 'agenda']);
    
    // Kontak
    Route::get('kontak', [AdminKontakController::class, 'index'])->name('kontak.index');
    Route::get('kontak/{kontak}', [AdminKontakController::class, 'show'])->name('kontak.show');
    Route::post('kontak/{kontak}/reply', [AdminKontakController::class, 'reply'])->name('kontak.reply');
    Route::delete('kontak/{kontak}', [AdminKontakController::class, 'destroy'])->name('kontak.destroy');
    
    // Pengaturan
    Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::get('pengaturan/template', [PengaturanController::class, 'template'])->name('pengaturan.template');
    Route::post('pengaturan/template', [PengaturanController::class, 'updateTemplate'])->name('pengaturan.template.update');

    // Menu Management
    Route::resource('menu', MenuController::class)->except(['show']);
    Route::post('menu/reorder', [MenuController::class, 'reorder'])->name('menu.reorder');

    // Database Backup & Restore
    Route::get('database', [DatabaseController::class, 'index'])->name('database.index');
    Route::post('database/backup', [DatabaseController::class, 'backup'])->name('database.backup');
    Route::get('database/download/{filename}', [DatabaseController::class, 'download'])->name('database.download');
    Route::delete('database/{filename}', [DatabaseController::class, 'destroy'])->name('database.destroy');
    Route::post('database/restore/{filename}', [DatabaseController::class, 'restore'])->name('database.restore');
    Route::post('database/upload', [DatabaseController::class, 'upload'])->name('database.upload');

    // Program Studi
    Route::resource('prodi', ProdiController::class);

    // Akreditasi
    Route::get('akreditasi/dashboard', [AkreditasiController::class, 'dashboard'])->name('akreditasi.dashboard');
    Route::resource('akreditasi', AkreditasiController::class);

    // AMI - Audit Mutu Internal
    Route::prefix('ami')->name('ami.')->group(function () {
        // Periode AMI
        Route::post('periode/{periode}/activate', [PeriodeAmiController::class, 'activate'])->name('periode.activate');
        Route::post('periode/{periode}/complete', [PeriodeAmiController::class, 'complete'])->name('periode.complete');
        Route::resource('periode', PeriodeAmiController::class);

        // Auditor
        Route::resource('auditor', AuditorController::class);

        // Jadwal AMI
        Route::post('jadwal/{jadwal}/update-status', [JadwalAmiController::class, 'updateStatus'])->name('jadwal.update-status');
        Route::post('jadwal/{jadwal}/add-auditor', [JadwalAmiController::class, 'addAuditor'])->name('jadwal.add-auditor');
        Route::delete('jadwal/{jadwal}/remove-auditor/{penugasan}', [JadwalAmiController::class, 'removeAuditor'])->name('jadwal.remove-auditor');
        Route::resource('jadwal', JadwalAmiController::class);

        // Temuan AMI
        Route::post('temuan/{temuan}/verify', [TemuanAmiController::class, 'verify'])->name('temuan.verify');
        Route::post('temuan/{temuan}/close', [TemuanAmiController::class, 'close'])->name('temuan.close');
        Route::post('temuan/{temuan}/reopen', [TemuanAmiController::class, 'reopen'])->name('temuan.reopen');
        Route::resource('temuan', TemuanAmiController::class);

        // Tindak Lanjut
        Route::get('tindak-lanjut/pending', [TindakLanjutController::class, 'pendingReview'])->name('tindak-lanjut.pending');
        Route::post('tindak-lanjut/{tindakLanjut}/review', [TindakLanjutController::class, 'review'])->name('tindak-lanjut.review');
        Route::resource('tindak-lanjut', TindakLanjutController::class);
    });

    // Statistik
    Route::get('statistik/chart', [StatistikController::class, 'chart'])->name('statistik.chart');
    Route::post('statistik/import', [StatistikController::class, 'import'])->name('statistik.import');
    Route::resource('statistik', StatistikController::class)->except(['show']);
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
