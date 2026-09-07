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
use App\Http\Controllers\Admin\FrontendMenuController;
use App\Http\Controllers\Admin\DatabaseController;
use App\Http\Controllers\Admin\ProdiController;
use App\Http\Controllers\Admin\AkreditasiController;
use App\Http\Controllers\Admin\PeriodeAmiController;
use App\Http\Controllers\Admin\AmiDashboardController;
use App\Http\Controllers\Admin\ButirInstrumenController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Admin\EvaluasiDiriController;
use App\Http\Controllers\Admin\LembarAuditController;
use App\Http\Controllers\Admin\BuktiAuditController;
use App\Http\Controllers\Admin\RtmController;
use App\Http\Controllers\Admin\AnalitikMutuController;
use App\Http\Controllers\Admin\VerifikasiRtlController;
use App\Http\Controllers\Admin\AuditorController;
use App\Http\Controllers\Admin\JadwalAmiController;
use App\Http\Controllers\Admin\TemuanAmiController;
use App\Http\Controllers\Admin\TindakLanjutController;
use App\Http\Controllers\Admin\StatistikController;
use App\Http\Controllers\Admin\PenugasanSayaController;
use App\Http\Controllers\Admin\StandarMutuController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PanduanController;
use App\Http\Controllers\Admin\DkpsController;
use App\Http\Controllers\Admin\DkpsMahasiswaController;
use App\Http\Controllers\Admin\DkpsDosenController;
use App\Http\Controllers\Admin\DkpsSaranaController;
use App\Http\Controllers\Admin\DkpsKurikulumController;
use App\Http\Controllers\Admin\DkpsLulusanController;
use App\Http\Controllers\Admin\DkpsPenelitianController;
use App\Http\Controllers\Admin\DkpsExportController;
use App\Http\Controllers\Admin\NotificationController;

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
Route::get('/dokumen/lihat/{slug}', [DokumenController::class, 'view'])->name('dokumen.view');
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

// Stop impersonating - available to the impersonated (possibly non-admin) user
Route::post('impersonate/leave', [UserController::class, 'leaveImpersonation'])
    ->middleware('auth')->name('impersonate.leave');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard - reachable by every admin-area role (admin/auditor/kaprodi/viewer)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');

    // Placeholder page for planned-but-unbuilt admin modules
    Route::get('coming-soon/{module}', [\App\Http\Controllers\Admin\ComingSoonController::class, 'show'])
        ->where('module', '[a-z0-9-]+')->name('coming-soon');

    // Pure admin-only content management - unchanged behavior, only admin role may enter
    Route::middleware('admin')->group(function () {
        // Berita
        Route::resource('berita', AdminBeritaController::class)->except(['show'])->parameters(['berita' => 'berita']);

        // Kategori Berita
        Route::resource('kategori-berita', KategoriBeritaController::class)->except(['show'])->parameters(['kategori-berita' => 'kategori']);

        // Galeri
        Route::resource('galeri', AdminGaleriController::class)->except(['show'])->parameters(['galeri' => 'galeri']);

        // Jenis Dokumen
        Route::resource('jenis-dokumen', JenisDokumenController::class)->except(['show'])->parameters(['jenis-dokumen' => 'jenisDokuman']);

        // Struktur Organisasi
        Route::resource('struktur-organisasi', StrukturOrganisasiController::class)->except(['show'])->parameters(['struktur-organisasi' => 'struktur']);

        // Halaman
        Route::resource('halaman', AdminHalamanController::class)->except(['show'])->parameters(['halaman' => 'halaman']);

        // Users
        Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
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

        // Menu Management - public website navbar
        Route::post('menu-web/tree', [FrontendMenuController::class, 'updateTree'])->name('menu-web.tree');
        Route::patch('menu-web/{menu}/quick', [FrontendMenuController::class, 'quickUpdate'])->name('menu-web.quick');
        Route::resource('menu-web', FrontendMenuController::class)->except(['show'])->parameters(['menu-web' => 'menu']);

        // Database Backup & Restore
        Route::get('database', [DatabaseController::class, 'index'])->name('database.index');
        Route::post('database/backup', [DatabaseController::class, 'backup'])->name('database.backup');
        Route::get('database/download/{filename}', [DatabaseController::class, 'download'])->name('database.download');
        Route::delete('database/{filename}', [DatabaseController::class, 'destroy'])->name('database.destroy');
        Route::post('database/restore/{filename}', [DatabaseController::class, 'restore'])->name('database.restore');
        Route::post('database/upload', [DatabaseController::class, 'upload'])->name('database.upload');

        // Statistik
        Route::get('statistik/chart', [StatistikController::class, 'chart'])->name('statistik.chart');
        Route::post('statistik/import', [StatistikController::class, 'import'])->name('statistik.import');
        Route::resource('statistik', StatistikController::class)->except(['show']);
    });

    // Program Studi / Akreditasi / AMI - permission-gated per-action via each controller's
    // own HasMiddleware::middleware(), NOT wrapped in the 'admin' middleware above, so
    // auditor/kaprodi/viewer roles can reach the actions their permissions allow.
    Route::resource('prodi', ProdiController::class);

    Route::get('akreditasi/dashboard', [AkreditasiController::class, 'dashboard'])->name('akreditasi.dashboard');
    Route::resource('akreditasi', AkreditasiController::class);

    Route::prefix('ami')->name('ami.')->group(function () {
        // Dashboard AMI + Monitoring + Audit Trail (Fase 1)
        Route::get('dashboard', [AmiDashboardController::class, 'index'])->name('dashboard');
        Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring');
        Route::get('audit-trail', [AuditTrailController::class, 'index'])->name('audit-trail')
            ->middleware('permission:audit-trail.view');

        // Instrumen Audit (butir per Standar Mutu)
        Route::resource('instrumen', ButirInstrumenController::class)->except(['show'])
            ->parameters(['instrumen' => 'instrumen']);

        // Evaluasi Diri (auditee) - Fase 2
        Route::get('evaluasi-diri', [EvaluasiDiriController::class, 'index'])->name('evaluasi-diri.index');
        Route::get('evaluasi-diri/{jadwal}', [EvaluasiDiriController::class, 'show'])->name('evaluasi-diri.show');
        Route::put('evaluasi-diri/{jadwal}', [EvaluasiDiriController::class, 'save'])->name('evaluasi-diri.save');
        Route::post('evaluasi-diri/{jadwal}/submit', [EvaluasiDiriController::class, 'submit'])->name('evaluasi-diri.submit');
        Route::post('evaluasi-diri/butir/{auditButir}/bukti', [EvaluasiDiriController::class, 'addBukti'])->name('evaluasi-diri.bukti.add');
        Route::delete('evaluasi-diri/bukti/{bukti}', [EvaluasiDiriController::class, 'deleteBukti'])->name('evaluasi-diri.bukti.delete');

        // Lembar Kerja Audit (auditor) - Fase 2
        Route::get('lembar-audit', [LembarAuditController::class, 'index'])->name('lembar-audit.index');
        Route::get('lembar-audit/{jadwal}', [LembarAuditController::class, 'show'])->name('lembar-audit.show');
        Route::put('lembar-audit/{jadwal}', [LembarAuditController::class, 'save'])->name('lembar-audit.save');
        Route::post('lembar-audit/bukti/{bukti}/validasi', [LembarAuditController::class, 'validateBukti'])->name('lembar-audit.bukti.validasi');

        // Dokumen / Bukti Audit (browse) - Fase 2
        Route::get('bukti', [BuktiAuditController::class, 'index'])->name('bukti.index');

        // Fase 3: Verifikasi RTL, Analitik Mutu, RTM
        Route::get('verifikasi-rtl', [VerifikasiRtlController::class, 'index'])->name('verifikasi-rtl.index');
        Route::post('verifikasi-rtl/{tindakLanjut}/verify', [VerifikasiRtlController::class, 'verify'])->name('verifikasi-rtl.verify');

        Route::get('analitik', [AnalitikMutuController::class, 'index'])->name('analitik');

        Route::post('rtm/{rtm}/generate-ringkasan', [RtmController::class, 'generateRingkasan'])->name('rtm.generate-ringkasan');
        Route::post('rtm/{rtm}/agenda', [RtmController::class, 'addAgenda'])->name('rtm.agenda.add');
        Route::put('rtm/agenda/{agenda}', [RtmController::class, 'updateAgenda'])->name('rtm.agenda.update');
        Route::delete('rtm/agenda/{agenda}', [RtmController::class, 'deleteAgenda'])->name('rtm.agenda.delete');
        Route::post('rtm/{rtm}/keputusan', [RtmController::class, 'addKeputusan'])->name('rtm.keputusan.add');
        Route::put('rtm/keputusan/{keputusan}', [RtmController::class, 'updateKeputusan'])->name('rtm.keputusan.update');
        Route::delete('rtm/keputusan/{keputusan}', [RtmController::class, 'deleteKeputusan'])->name('rtm.keputusan.delete');
        Route::resource('rtm', RtmController::class);

        // Periode AMI
        Route::post('periode/{periode}/activate', [PeriodeAmiController::class, 'activate'])->name('periode.activate');
        Route::post('periode/{periode}/complete', [PeriodeAmiController::class, 'complete'])->name('periode.complete');
        Route::resource('periode', PeriodeAmiController::class);

        // Auditor
        Route::resource('auditor', AuditorController::class);

        // Penugasan Saya - an auditor's own assignment inbox (accept/reject)
        Route::get('penugasan/saya', [PenugasanSayaController::class, 'index'])->name('penugasan.saya');
        Route::post('penugasan/{penugasan}/accept', [PenugasanSayaController::class, 'accept'])->name('penugasan.accept');
        Route::post('penugasan/{penugasan}/reject', [PenugasanSayaController::class, 'reject'])->name('penugasan.reject');

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

        // Standar Mutu
        Route::resource('standar-mutu', StandarMutuController::class)->except(['show']);
    });

    // Laporan - permission-gated per-action (view the menu vs. actually export),
    // not wrapped in the 'admin' middleware, same reasoning as the AMI group above.
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('temuan/export', [LaporanController::class, 'exportTemuan'])->name('temuan.export');
        Route::get('tindak-lanjut/export', [LaporanController::class, 'exportTindakLanjut'])->name('tindak-lanjut.export');
        Route::get('akreditasi/export', [LaporanController::class, 'exportAkreditasi'])->name('akreditasi.export');
    });

    // Buku Panduan - permission-gated per-action, not wrapped in the 'admin'
    // middleware, so every logged-in role can read it; only panduan.manage
    // can add/edit/delete chapters.
    Route::get('panduan/export-pdf', [PanduanController::class, 'exportPdf'])->name('panduan.export-pdf');
    Route::resource('panduan', PanduanController::class)->except(['show']);

    // Dokumen - permission-gated per-action, not wrapped in the 'admin' middleware,
    // so Asesor (dokumen.view only) and Dosen (dokumen.view + dokumen.upload) can
    // reach the actions their permissions allow; edit/delete/workflow stay admin-only.
    Route::post('dokumen/{dokumen}/submit', [AdminDokumenController::class, 'submit'])->name('dokumen.submit');
    Route::post('dokumen/{dokumen}/approve', [AdminDokumenController::class, 'approve'])->name('dokumen.approve');
    Route::post('dokumen/{dokumen}/reject', [AdminDokumenController::class, 'reject'])->name('dokumen.reject');
    Route::resource('dokumen', AdminDokumenController::class)->parameters(['dokumen' => 'dokumen']);

    // DKPS - permission-gated per-action, not wrapped in the 'admin' middleware,
    // so kaprodi (dkps.manage) and other roles (dkps.view) can reach it. The
    // 'dkps.own' middleware additionally blocks a kaprodi from reaching any
    // other prodi's submission or its nested section routes.
    Route::resource('dkps', DkpsController::class)->middleware('dkps.own');
    Route::get('dkps/{dkp}/export', [DkpsExportController::class, 'export'])->name('dkps.export')->middleware('dkps.own');

    Route::prefix('dkps/{dkp}')->name('dkps.')->middleware('dkps.own')->group(function () {
        // Kerjasama (Tabel 1.1-1.3)
        Route::post('kerjasama', [DkpsMahasiswaController::class, 'storeKerjasama'])->name('kerjasama.store');
        Route::put('kerjasama/{kerjasama}', [DkpsMahasiswaController::class, 'updateKerjasama'])->name('kerjasama.update');
        Route::delete('kerjasama/{kerjasama}', [DkpsMahasiswaController::class, 'destroyKerjasama'])->name('kerjasama.destroy');

        // Kualitas Input Mahasiswa (Tabel 2, fixed rows)
        Route::put('kualitas-input', [DkpsMahasiswaController::class, 'updateKualitasInput'])->name('kualitas-input.update');

        // Prestasi Mahasiswa (Tabel 3)
        Route::post('prestasi', [DkpsMahasiswaController::class, 'storePrestasi'])->name('prestasi.store');
        Route::put('prestasi/{prestasi}', [DkpsMahasiswaController::class, 'updatePrestasi'])->name('prestasi.update');
        Route::delete('prestasi/{prestasi}', [DkpsMahasiswaController::class, 'destroyPrestasi'])->name('prestasi.destroy');

        // Karya Inovatif Mahasiswa (Tabel 4.1-4.4)
        Route::post('karya-inovatif', [DkpsMahasiswaController::class, 'storeKaryaInovatif'])->name('karya-inovatif.store');
        Route::put('karya-inovatif/{karya}', [DkpsMahasiswaController::class, 'updateKaryaInovatif'])->name('karya-inovatif.update');
        Route::delete('karya-inovatif/{karya}', [DkpsMahasiswaController::class, 'destroyKaryaInovatif'])->name('karya-inovatif.destroy');

        // Kepuasan Mahasiswa (Tabel 5, fixed rows)
        Route::put('kepuasan-mahasiswa', [DkpsMahasiswaController::class, 'updateKepuasan'])->name('kepuasan-mahasiswa.update');

        // Dosen Tetap (Tabel 6)
        Route::post('dosen', [DkpsDosenController::class, 'storeDosen'])->name('dosen.store');
        Route::put('dosen/{dosen}', [DkpsDosenController::class, 'updateDosen'])->name('dosen.update');
        Route::delete('dosen/{dosen}', [DkpsDosenController::class, 'destroyDosen'])->name('dosen.destroy');

        // Beban Kerja DTPS (Tabel 7)
        Route::post('beban-kerja', [DkpsDosenController::class, 'storeBebanKerja'])->name('beban-kerja.store');
        Route::put('beban-kerja/{beban}', [DkpsDosenController::class, 'updateBebanKerja'])->name('beban-kerja.update');
        Route::delete('beban-kerja/{beban}', [DkpsDosenController::class, 'destroyBebanKerja'])->name('beban-kerja.destroy');

        // Rekognisi DTPS (Tabel 8)
        Route::post('rekognisi', [DkpsDosenController::class, 'storeRekognisi'])->name('rekognisi.store');
        Route::put('rekognisi/{rekognisi}', [DkpsDosenController::class, 'updateRekognisi'])->name('rekognisi.update');
        Route::delete('rekognisi/{rekognisi}', [DkpsDosenController::class, 'destroyRekognisi'])->name('rekognisi.destroy');

        // Pengembangan Kompetensi (Tabel 9 dosen / Tabel 11 tendik)
        Route::post('pengembangan', [DkpsDosenController::class, 'storePengembangan'])->name('pengembangan.store');
        Route::put('pengembangan/{pengembangan}', [DkpsDosenController::class, 'updatePengembangan'])->name('pengembangan.update');
        Route::delete('pengembangan/{pengembangan}', [DkpsDosenController::class, 'destroyPengembangan'])->name('pengembangan.destroy');

        // Tenaga Kependidikan roster (for Tabel 11)
        Route::post('tendik', [DkpsDosenController::class, 'storeTendik'])->name('tendik.store');
        Route::put('tendik/{tendik}', [DkpsDosenController::class, 'updateTendik'])->name('tendik.update');
        Route::delete('tendik/{tendik}', [DkpsDosenController::class, 'destroyTendik'])->name('tendik.destroy');

        // Tenaga Kependidikan Summary (Tabel 10, fixed rows)
        Route::put('tendik-summary', [DkpsDosenController::class, 'updateTendikSummary'])->name('tendik-summary.update');

        // Penggunaan Dana (Tabel 12, fixed rows)
        Route::put('penggunaan-dana', [DkpsSaranaController::class, 'updatePenggunaanDana'])->name('penggunaan-dana.update');

        // Sarana Lab (Tabel 13)
        Route::post('sarana-lab', [DkpsSaranaController::class, 'storeSaranaLab'])->name('sarana-lab.store');
        Route::put('sarana-lab/{sarana}', [DkpsSaranaController::class, 'updateSaranaLab'])->name('sarana-lab.update');
        Route::delete('sarana-lab/{sarana}', [DkpsSaranaController::class, 'destroySaranaLab'])->name('sarana-lab.destroy');

        // Prasarana (Tabel 14)
        Route::post('prasarana', [DkpsSaranaController::class, 'storePrasarana'])->name('prasarana.store');
        Route::put('prasarana/{prasarana}', [DkpsSaranaController::class, 'updatePrasarana'])->name('prasarana.update');
        Route::delete('prasarana/{prasarana}', [DkpsSaranaController::class, 'destroyPrasarana'])->name('prasarana.destroy');

        // TIK (Tabel 15)
        Route::post('tik', [DkpsSaranaController::class, 'storeTik'])->name('tik.store');
        Route::put('tik/{tik}', [DkpsSaranaController::class, 'updateTik'])->name('tik.update');
        Route::delete('tik/{tik}', [DkpsSaranaController::class, 'destroyTik'])->name('tik.destroy');

        // Kurikulum (Tabel 16)
        Route::post('kurikulum', [DkpsKurikulumController::class, 'storeKurikulum'])->name('kurikulum.store');
        Route::put('kurikulum/{kurikulum}', [DkpsKurikulumController::class, 'updateKurikulum'])->name('kurikulum.update');
        Route::delete('kurikulum/{kurikulum}', [DkpsKurikulumController::class, 'destroyKurikulum'])->name('kurikulum.destroy');

        // Integrasi Penelitian/PkM (Tabel 17)
        Route::post('integrasi', [DkpsKurikulumController::class, 'storeIntegrasi'])->name('integrasi.store');
        Route::put('integrasi/{integrasi}', [DkpsKurikulumController::class, 'updateIntegrasi'])->name('integrasi.update');
        Route::delete('integrasi/{integrasi}', [DkpsKurikulumController::class, 'destroyIntegrasi'])->name('integrasi.destroy');

        // Pembimbingan Magang Kependidikan (Tabel 18)
        Route::post('pembimbingan-magang', [DkpsKurikulumController::class, 'storePembimbinganMagang'])->name('pembimbingan-magang.store');
        Route::put('pembimbingan-magang/{magang}', [DkpsKurikulumController::class, 'updatePembimbinganMagang'])->name('pembimbingan-magang.update');
        Route::delete('pembimbingan-magang/{magang}', [DkpsKurikulumController::class, 'destroyPembimbinganMagang'])->name('pembimbingan-magang.destroy');

        // Kegiatan Akademik di Luar Kelas (Tabel 19)
        Route::post('kegiatan-luar-kelas', [DkpsKurikulumController::class, 'storeKegiatanLuarKelas'])->name('kegiatan-luar-kelas.store');
        Route::put('kegiatan-luar-kelas/{kegiatan}', [DkpsKurikulumController::class, 'updateKegiatanLuarKelas'])->name('kegiatan-luar-kelas.update');
        Route::delete('kegiatan-luar-kelas/{kegiatan}', [DkpsKurikulumController::class, 'destroyKegiatanLuarKelas'])->name('kegiatan-luar-kelas.destroy');

        // Pembimbingan Tugas Akhir/Skripsi (Tabel 20)
        Route::post('pembimbingan-ta', [DkpsKurikulumController::class, 'storePembimbinganTa'])->name('pembimbingan-ta.store');
        Route::put('pembimbingan-ta/{ta}', [DkpsKurikulumController::class, 'updatePembimbinganTa'])->name('pembimbingan-ta.update');
        Route::delete('pembimbingan-ta/{ta}', [DkpsKurikulumController::class, 'destroyPembimbinganTa'])->name('pembimbingan-ta.destroy');

        // IPK Lulusan (Tabel 21, fixed rows)
        Route::put('ipk-lulusan', [DkpsLulusanController::class, 'updateIpk'])->name('ipk-lulusan.update');

        // Masa Studi Lulusan (Tabel 22, fixed rows)
        Route::put('masa-studi', [DkpsLulusanController::class, 'updateMasaStudi'])->name('masa-studi.update');

        // Lulusan Bekerja & Studi Lanjut (Tabel 23, fixed rows)
        Route::put('lulusan-bekerja', [DkpsLulusanController::class, 'updateLulusanBekerja'])->name('lulusan-bekerja.update');

        // Waktu Tunggu (Tabel 24, fixed rows)
        Route::put('waktu-tunggu', [DkpsLulusanController::class, 'updateWaktuTunggu'])->name('waktu-tunggu.update');

        // Kesesuaian Bidang (Tabel 25, fixed rows)
        Route::put('kesesuaian-bidang', [DkpsLulusanController::class, 'updateKesesuaianBidang'])->name('kesesuaian-bidang.update');

        // Kepuasan Pengguna (Tabel 26, fixed rows)
        Route::put('kepuasan-pengguna', [DkpsLulusanController::class, 'updateKepuasanPengguna'])->name('kepuasan-pengguna.update');

        // Penelitian/PkM Ringkasan (Tabel 27/32, fixed rows)
        Route::put('penelitian-ringkasan', [DkpsPenelitianController::class, 'updateRingkasan'])->name('penelitian-ringkasan.update');

        // Penelitian/PkM Melibatkan Mahasiswa (Tabel 28/33)
        Route::post('penelitian-mahasiswa', [DkpsPenelitianController::class, 'storeMahasiswa'])->name('penelitian-mahasiswa.store');
        Route::put('penelitian-mahasiswa/{item}', [DkpsPenelitianController::class, 'updateMahasiswa'])->name('penelitian-mahasiswa.update');
        Route::delete('penelitian-mahasiswa/{item}', [DkpsPenelitianController::class, 'destroyMahasiswa'])->name('penelitian-mahasiswa.destroy');

        // Publikasi Ilmiah DTPS (Tabel 29, fixed rows)
        Route::put('publikasi-dtps', [DkpsPenelitianController::class, 'updatePublikasi'])->name('publikasi-dtps.update');

        // Publikasi DTPS Sinta/Scopus (Tabel 30)
        Route::post('publikasi-detail', [DkpsPenelitianController::class, 'storeDetail'])->name('publikasi-detail.store');
        Route::put('publikasi-detail/{detail}', [DkpsPenelitianController::class, 'updateDetail'])->name('publikasi-detail.update');
        Route::delete('publikasi-detail/{detail}', [DkpsPenelitianController::class, 'destroyDetail'])->name('publikasi-detail.destroy');

        // Sitasi DTPS (Tabel 31)
        Route::post('sitasi-dtps', [DkpsPenelitianController::class, 'storeSitasi'])->name('sitasi-dtps.store');
        Route::put('sitasi-dtps/{sitasi}', [DkpsPenelitianController::class, 'updateSitasi'])->name('sitasi-dtps.update');
        Route::delete('sitasi-dtps/{sitasi}', [DkpsPenelitianController::class, 'destroySitasi'])->name('sitasi-dtps.destroy');
    });

    // Notifications - any logged-in admin-area user, scoped to their own data only
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
