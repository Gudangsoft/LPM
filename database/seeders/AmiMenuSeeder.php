<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

/**
 * Builds the "PENJAMINAN MUTU" part of the admin sidebar and normalises the
 * order of every top-level admin menu item.
 *
 *   SPMI          – siklus PPEPP (kebijakan/dokumen sisi P-1/P-2; P-3/P-4 berupa tautan
 *                   pintas ke halaman yang sama dengan grup AMI supaya penomoran
 *                   P-1..P-4 tidak bolong):
 *                     P-1 Penetapan, P-2 Pelaksanaan,
 *                     P-3 Pengendalian (-> Tindak Lanjut, Verifikasi RTL, Monitoring),
 *                     P-4 Peningkatan (-> RTM, dst.),
 *                     E-Evaluasi (Non-AMI) - ditaruh terakhir, bukan di posisi PPEPP
 *                     baku, karena isinya masih semua placeholder
 *   AMI           – seluruh operasional audit, rata satu tingkat (menu kerja utama): Dashboard,
 *                   Periode, Auditor, Jadwal, Penugasan, Evaluasi Diri, Lembar Kerja, Temuan,
 *                   Tindak Lanjut, Verifikasi RTL, Monitoring, Dokumen/Bukti, RTM, Audit Trail
 *   Akreditasi    – Data Akreditasi, Dashboard Akreditasi
 *   Laporan       – Laporan AMI, Analitik Mutu, Data Statistik, Grafik
 *
 * Item tanpa fitur nyata mengarah ke halaman placeholder "coming soon".
 */
class AmiMenuSeeder extends Seeder
{
    /** Real leaf routes that belong to this section (used to clean up the old flat menu). */
    private const LEAF_ROUTES = [
        'admin.prodi.index',
        'admin.akreditasi.index',
        'admin.akreditasi.dashboard',
        'admin.ami.periode.index',
        'admin.ami.auditor.index',
        'admin.ami.jadwal.index',
        'admin.ami.penugasan.saya',
        'admin.ami.temuan.index',
        'admin.ami.tindak-lanjut.index',
        'admin.ami.standar-mutu.index',
        'admin.laporan.index',
        'admin.panduan.index',
        'admin.dkps.index',
        'admin.statistik.index',
        'admin.statistik.chart',
    ];

    /** Desired top-level order (matched by route, or by name for section headers / groups). */
    private const ROOT_ORDER = [
        ['route' => 'admin.dashboard'],
        ['section' => 'PENJAMINAN MUTU'],
        ['group' => 'SPMI'],
        ['group' => 'AMI'],
        ['group' => 'Akreditasi'],
        ['group' => 'Laporan'],
        ['section' => 'Konten'],
        ['route' => 'admin.berita.index'],
        ['route' => 'admin.kategori-berita.index'],
        ['route' => 'admin.pengumuman.index'],
        ['route' => 'admin.agenda.index'],
        ['section' => 'Media'],
        ['route' => 'admin.galeri.index'],
        ['route' => 'admin.sliders.index'],
        ['route' => 'admin.dokumen.index'],
        ['route' => 'admin.jenis-dokumen.index'],
        ['section' => 'Halaman'],
        ['route' => 'admin.halaman.index'],
        ['route' => 'admin.struktur-organisasi.index'],
        ['section' => 'Sistem'],
        ['route' => 'admin.menu-web.index'],
        ['route' => 'admin.users.index'],
        ['route' => 'admin.kontak.index'],
        ['route' => 'admin.pengaturan.index'],
        ['route' => 'admin.database.index'],
    ];

    public function run(): void
    {
        self::rebuild();
    }

    /** Idempotent: wipe the previous SPMI / Laporan branch, rebuild it, re-sort roots. */
    public static function rebuild(): void
    {
        Menu::where('lokasi', 'admin')
            ->whereNull('parent_id')
            ->whereIn('nama', ['SPMI', 'AMI', 'Akreditasi', 'Laporan', 'PENJAMINAN MUTU', 'AUDIT MUTU INTERNAL'])
            ->get()->each->delete();

        Menu::where('lokasi', 'admin')->where('tipe', 'admin')->get()->each->delete();

        Menu::where('lokasi', 'admin')->whereIn('route', self::LEAF_ROUTES)->delete();

        // ---- section header -------------------------------------------------
        Menu::create([
            'nama' => 'PENJAMINAN MUTU',
            'tipe' => 'section',
            'lokasi' => 'admin',
            'urutan' => 2,
            'is_active' => true,
        ]);

        // ---- SPMI: penetapan standar & dokumen (PPEPP untuk sisi kebijakan) ----
        $spmi = self::node(null, 3, ['nama' => 'SPMI', 'icon' => 'bi-diagram-3']);

        $p1 = self::node($spmi, 1, ['nama' => 'P-1 Penetapan', 'icon' => 'bi-1-circle']);
        self::dok($p1, 1, 'Kebijakan SPMI', 'kebijakan-spmi', 'bi-shield-check');
        self::dok($p1, 2, 'Manual SPMI', 'manual-spmi', 'bi-book');
        self::node($p1, 3, ['nama' => 'Standar Mutu', 'route' => 'admin.ami.standar-mutu.index', 'route_pattern' => 'admin.ami.standar-mutu.*', 'icon' => 'bi-list-check', 'permission' => 'standar-mutu.view']);
        self::node($p1, 4, ['nama' => 'Instrumen Audit', 'route' => 'admin.ami.instrumen.index', 'route_pattern' => 'admin.ami.instrumen.*', 'icon' => 'bi-ui-checks-grid', 'permission' => 'standar-mutu.view']);
        self::dok($p1, 5, 'Formulir SPMI', 'formulir', 'bi-ui-checks');
        self::node($p1, 6, ['nama' => 'Program Studi', 'route' => 'admin.prodi.index', 'route_pattern' => 'admin.prodi.*', 'icon' => 'bi-mortarboard', 'permission' => 'prodi.view']);
        // Buku Panduan sengaja tidak ada di sini - itu panduan pemakaian aplikasi
        // (lintas modul), bukan dokumen kebijakan SPMI. Aksesnya lewat ikon
        // bantuan (?) di header, bukan sidebar SPMI.

        $p2 = self::node($spmi, 2, ['nama' => 'P-2 Pelaksanaan', 'icon' => 'bi-2-circle']);
        self::node($p2, 1, ['nama' => 'DKPS', 'route' => 'admin.dkps.index', 'route_pattern' => 'admin.dkps.*', 'icon' => 'bi-clipboard-data', 'permission' => 'dkps.view']);
        self::cs($p2, 2, 'Sasaran Mutu', 'sasaran-mutu', 'bi-bullseye');
        self::cs($p2, 3, 'Pengendalian Dokumen', 'pengendalian-dokumen', 'bi-folder-check');

        // Pengendalian itu sendiri hidup di grup AMI (operasional); di sini hanya
        // tautan pintas ke halaman yang sama, supaya siklus PPEPP di bawah SPMI lengkap.
        $p3 = self::node($spmi, 3, ['nama' => 'P-3 Pengendalian', 'icon' => 'bi-3-circle']);
        self::node($p3, 1, ['nama' => 'Tindak Lanjut', 'route' => 'admin.ami.tindak-lanjut.index', 'route_pattern' => 'admin.ami.tindak-lanjut.*', 'icon' => 'bi-clipboard-check', 'permission' => 'tindak-lanjut.view']);
        self::node($p3, 2, ['nama' => 'Verifikasi RTL', 'route' => 'admin.ami.verifikasi-rtl.index', 'route_pattern' => 'admin.ami.verifikasi-rtl.*', 'icon' => 'bi-check2-square', 'permission' => 'tindak-lanjut.view']);
        self::node($p3, 3, ['nama' => 'Monitoring', 'route' => 'admin.ami.monitoring', 'route_pattern' => 'admin.ami.monitoring', 'icon' => 'bi-activity', 'permission' => 'tindak-lanjut.view']);

        $p4 = self::node($spmi, 4, ['nama' => 'P-4 Peningkatan', 'icon' => 'bi-4-circle']);
        self::node($p4, 1, ['nama' => 'Rapat Tinjauan Manajemen', 'route' => 'admin.ami.rtm.index', 'route_pattern' => 'admin.ami.rtm.*', 'icon' => 'bi-people', 'permission' => 'rtm.view']);
        self::cs($p4, 2, 'Rencana Peningkatan Mutu', 'rencana-peningkatan-mutu', 'bi-graph-up');
        self::cs($p4, 3, 'Benchmarking', 'benchmarking', 'bi-bar-chart-steps');

        // Ditaruh terakhir (bukan di posisi PPEPP baku P-2/P-3) karena seluruh isinya
        // masih placeholder - biar tidak memutus alur P-1..P-4 yang sudah berfungsi.
        $ev = self::node($spmi, 5, ['nama' => 'E-Evaluasi (Non-AMI)', 'icon' => 'bi-clipboard-check']);
        self::cs($ev, 1, 'Survey Kepuasan', 'survey-kepuasan', 'bi-emoji-smile');
        self::cs($ev, 2, 'Monev', 'monev', 'bi-binoculars');
        self::cs($ev, 3, 'Evaluasi Pembelajaran', 'evaluasi-pembelajaran', 'bi-easel');
        self::cs($ev, 4, 'Capaian Pembelajaran', 'capaian-pembelajaran', 'bi-graph-up-arrow');
        self::node($ev, 5, ['nama' => 'Review RPS', 'route' => 'admin.ami.rps-review.index', 'route_pattern' => 'admin.ami.rps-review.*', 'icon' => 'bi-file-earmark-text', 'permission' => 'rps.view']);

        // ---- AMI: seluruh operasional audit, rata di satu tingkat -------------
        $ami = self::node(null, 4, ['nama' => 'AMI', 'icon' => 'bi-search']);
        self::node($ami, 1, ['nama' => 'Dashboard AMI', 'route' => 'admin.ami.dashboard', 'route_pattern' => 'admin.ami.dashboard', 'icon' => 'bi-speedometer2', 'permission' => 'periode-ami.view']);
        // Tidak ada permission granular untuk Pengaturan (lihat menu "Pengaturan" itu
        // sendiri) - admin-only secara implisit lewat Menu::isVisibleTo().
        self::node($ami, 2, ['nama' => 'Aturan AMI', 'url' => '/admin/pengaturan?tab=ami', 'icon' => 'bi-gear']);
        self::node($ami, 3, ['nama' => 'Periode AMI', 'route' => 'admin.ami.periode.index', 'route_pattern' => 'admin.ami.periode.*', 'icon' => 'bi-calendar3', 'permission' => 'periode-ami.view']);
        self::node($ami, 4, ['nama' => 'Auditor', 'route' => 'admin.ami.auditor.index', 'route_pattern' => 'admin.ami.auditor.*', 'icon' => 'bi-person-badge', 'permission' => 'auditor.view']);
        self::node($ami, 5, ['nama' => 'Jadwal Audit', 'route' => 'admin.ami.jadwal.index', 'route_pattern' => 'admin.ami.jadwal.*', 'icon' => 'bi-calendar-check', 'permission' => 'jadwal-ami.view']);
        self::node($ami, 6, ['nama' => 'Penugasan Saya', 'route' => 'admin.ami.penugasan.saya', 'route_pattern' => 'admin.ami.penugasan.*', 'icon' => 'bi-inbox', 'permission' => 'penugasan.respond']);
        self::node($ami, 7, ['nama' => 'Evaluasi Diri', 'route' => 'admin.ami.evaluasi-diri.index', 'route_pattern' => 'admin.ami.evaluasi-diri.*', 'icon' => 'bi-pencil-square', 'permission' => 'evaluasi-diri.view']);
        self::node($ami, 8, ['nama' => 'Lembar Kerja Audit', 'route' => 'admin.ami.lembar-audit.index', 'route_pattern' => 'admin.ami.lembar-audit.*', 'icon' => 'bi-clipboard2-check', 'permission' => 'lembar-audit.view']);
        self::node($ami, 9, ['nama' => 'Temuan', 'route' => 'admin.ami.temuan.index', 'route_pattern' => 'admin.ami.temuan.*', 'icon' => 'bi-exclamation-diamond', 'permission' => 'temuan.view']);
        self::node($ami, 10, [
            'nama' => 'Tindak Lanjut',
            'route' => 'admin.ami.tindak-lanjut.index',
            'route_pattern' => 'admin.ami.tindak-lanjut.*',
            'icon' => 'bi-clipboard-check',
            'permission' => 'tindak-lanjut.view',
            'badge_model' => 'App\\Models\\TindakLanjut',
            'badge_method' => 'pendingReview',
            'badge_class' => 'bg-warning',
        ]);
        self::node($ami, 11, ['nama' => 'Verifikasi RTL', 'route' => 'admin.ami.verifikasi-rtl.index', 'route_pattern' => 'admin.ami.verifikasi-rtl.*', 'icon' => 'bi-check2-square', 'permission' => 'tindak-lanjut.view']);
        self::node($ami, 12, ['nama' => 'Monitoring', 'route' => 'admin.ami.monitoring', 'route_pattern' => 'admin.ami.monitoring', 'icon' => 'bi-activity', 'permission' => 'tindak-lanjut.view']);
        self::node($ami, 13, ['nama' => 'Dokumen / Bukti', 'route' => 'admin.ami.bukti.index', 'route_pattern' => 'admin.ami.bukti.*', 'icon' => 'bi-folder2-open', 'permission' => 'bukti.view']);
        self::node($ami, 14, ['nama' => 'Rapat Tinjauan Manajemen', 'route' => 'admin.ami.rtm.index', 'route_pattern' => 'admin.ami.rtm.*', 'icon' => 'bi-people', 'permission' => 'rtm.view']);
        self::node($ami, 15, ['nama' => 'Audit Trail', 'route' => 'admin.ami.audit-trail', 'route_pattern' => 'admin.ami.audit-trail', 'icon' => 'bi-clock-history', 'permission' => 'audit-trail.view']);

        // ---- Akreditasi (grup top-level) -------------------------------------
        $akr = self::node(null, 5, ['nama' => 'Akreditasi', 'icon' => 'bi-award']);
        self::node($akr, 1, ['nama' => 'Data Akreditasi', 'route' => 'admin.akreditasi.index', 'route_pattern' => 'admin.akreditasi.*', 'icon' => 'bi-award', 'permission' => 'akreditasi.view']);
        self::node($akr, 2, ['nama' => 'Dashboard Akreditasi', 'route' => 'admin.akreditasi.dashboard', 'icon' => 'bi-speedometer2', 'permission' => 'akreditasi.view']);

        // ---- Laporan (grup top-level) --------------------------------------
        $laporan = self::node(null, 6, ['nama' => 'Laporan', 'icon' => 'bi-bar-chart-line']);
        self::node($laporan, 1, ['nama' => 'Laporan AMI', 'route' => 'admin.laporan.index', 'route_pattern' => 'admin.laporan.*', 'icon' => 'bi-file-earmark-bar-graph', 'permission' => 'laporan-ami.view']);
        self::node($laporan, 2, ['nama' => 'Analitik Mutu', 'route' => 'admin.ami.analitik', 'route_pattern' => 'admin.ami.analitik', 'icon' => 'bi-graph-up-arrow', 'permission' => 'laporan-ami.view']);
        self::node($laporan, 3, ['nama' => 'Data Statistik', 'route' => 'admin.statistik.index', 'icon' => 'bi-table']);
        self::node($laporan, 4, ['nama' => 'Grafik', 'route' => 'admin.statistik.chart', 'icon' => 'bi-bar-chart']);

        self::normalizeRootOrder();

        Menu::clearCache();
    }

    // ---------------------------------------------------------------------

    private static function node(?int $parentId, int $urutan, array $attr): int
    {
        return Menu::create(array_merge([
            'tipe' => 'link',
            'lokasi' => 'admin',
            'parent_id' => $parentId,
            'urutan' => $urutan,
            'is_active' => true,
            'badge_class' => 'bg-danger',
        ], $attr))->id;
    }

    /** Placeholder ("coming soon") leaf. */
    private static function cs(int $parentId, int $urutan, string $nama, string $slug, string $icon): int
    {
        return self::node($parentId, $urutan, [
            'nama' => $nama,
            'url' => '/admin/coming-soon/' . $slug,
            'icon' => $icon,
        ]);
    }

    /**
     * Leaf that opens the real Dokumen module pre-filtered to one jenis_dokumen
     * (e.g. Kebijakan SPMI, Manual SPMI, Formulir) - reuses the existing document
     * repository (upload, versioning, draft/submit/approve workflow) instead of
     * a one-off feature per document type. Falls back to a "coming soon"
     * placeholder if that jenis_dokumen row doesn't exist yet.
     */
    private static function dok(int $parentId, int $urutan, string $nama, string $jenisSlug, string $icon): int
    {
        $jenis = \App\Models\JenisDokumen::where('slug', $jenisSlug)->first();

        if (! $jenis) {
            return self::cs($parentId, $urutan, $nama, $jenisSlug, $icon);
        }

        return self::node($parentId, $urutan, [
            'nama' => $nama,
            'url' => '/admin/dokumen?jenis=' . $jenis->id,
            'icon' => $icon,
            'permission' => 'dokumen.view',
        ]);
    }

    private static function normalizeRootOrder(): void
    {
        $used = [];
        $pos = 1;

        foreach (self::ROOT_ORDER as $spec) {
            $q = Menu::where('lokasi', 'admin')->whereNull('parent_id');

            if (isset($spec['route'])) {
                $q->where('route', $spec['route']);
            } elseif (isset($spec['section'])) {
                $q->where('nama', $spec['section'])->where('tipe', 'section');
            } else {
                $q->where('nama', $spec['group'])->whereNull('route')->where('tipe', 'link');
            }

            $row = $q->first();
            if ($row) {
                $row->update(['urutan' => $pos]);
                $used[] = $row->id;
            }
            $pos++;
        }

        // Anything not in the list keeps its relative order, appended at the end.
        $tail = 200;
        Menu::where('lokasi', 'admin')->whereNull('parent_id')
            ->whereNotIn('id', $used)
            ->orderBy('urutan')->orderBy('id')
            ->get()
            ->each(function ($row) use (&$tail) {
                $row->update(['urutan' => $tail++]);
            });
    }
}
