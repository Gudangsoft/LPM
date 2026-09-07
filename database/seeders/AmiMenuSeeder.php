<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

/**
 * Builds the "PENJAMINAN MUTU" part of the admin sidebar as a PPEPP tree and
 * normalises the order of every top-level admin menu item.
 *
 *   SPMI
 *   ├─ P-1 Penetapan      (Kebijakan/Manual/Standar/Formulir SPMI, Program Studi, Buku Panduan)
 *   ├─ P-2 Pelaksanaan    (DKPS, Sasaran Mutu, Pengendalian Dokumen)
 *   ├─ E-Evaluasi         (Audit Mutu Internal, Akreditasi, Survey Kepuasan, Monev,
 *   │                      Evaluasi/Capaian Pembelajaran, Review RPS)
 *   ├─ P-3 Pengendalian   (Tindak Lanjut, Monitoring)
 *   └─ P-4 Peningkatan    (RTM, Rencana Peningkatan Mutu, Benchmarking)
 *   Laporan               (Laporan AMI, Data Statistik, Grafik)
 *
 * Items without a real feature yet point to the "coming soon" placeholder.
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
        ['route' => 'admin.menu.index'],
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
            ->whereIn('nama', ['SPMI', 'Laporan', 'PENJAMINAN MUTU', 'AUDIT MUTU INTERNAL'])
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

        // ---- SPMI (PPEPP) -------------------------------------------------
        $spmi = self::node(null, 3, ['nama' => 'SPMI', 'icon' => 'bi-diagram-3']);

        $p1 = self::node($spmi, 1, ['nama' => 'P-1 Penetapan', 'icon' => 'bi-1-circle']);
        self::cs($p1, 1, 'Kebijakan SPMI', 'kebijakan-spmi', 'bi-shield-check');
        self::cs($p1, 2, 'Manual SPMI', 'manual-spmi', 'bi-book');
        self::node($p1, 3, ['nama' => 'Standar Mutu', 'route' => 'admin.ami.standar-mutu.index', 'route_pattern' => 'admin.ami.standar-mutu.*', 'icon' => 'bi-list-check', 'permission' => 'standar-mutu.view']);
        self::cs($p1, 4, 'Formulir SPMI', 'formulir-spmi', 'bi-ui-checks');
        self::node($p1, 5, ['nama' => 'Program Studi', 'route' => 'admin.prodi.index', 'route_pattern' => 'admin.prodi.*', 'icon' => 'bi-mortarboard', 'permission' => 'prodi.view']);
        self::node($p1, 6, ['nama' => 'Buku Panduan', 'route' => 'admin.panduan.index', 'route_pattern' => 'admin.panduan.*', 'icon' => 'bi-journal-bookmark', 'permission' => 'panduan.view']);

        $p2 = self::node($spmi, 2, ['nama' => 'P-2 Pelaksanaan', 'icon' => 'bi-2-circle']);
        self::node($p2, 1, ['nama' => 'DKPS', 'route' => 'admin.dkps.index', 'route_pattern' => 'admin.dkps.*', 'icon' => 'bi-clipboard-data', 'permission' => 'dkps.view']);
        self::cs($p2, 2, 'Sasaran Mutu', 'sasaran-mutu', 'bi-bullseye');
        self::cs($p2, 3, 'Pengendalian Dokumen', 'pengendalian-dokumen', 'bi-folder-check');

        $ev = self::node($spmi, 3, ['nama' => 'E-Evaluasi', 'icon' => 'bi-clipboard-check']);

        $ami = self::node($ev, 1, ['nama' => 'Audit Mutu Internal', 'icon' => 'bi-search']);
        self::cs($ami, 1, 'Aturan AMI', 'aturan-ami', 'bi-gear');
        self::node($ami, 2, ['nama' => 'Periode AMI', 'route' => 'admin.ami.periode.index', 'route_pattern' => 'admin.ami.periode.*', 'icon' => 'bi-calendar3', 'permission' => 'periode-ami.view']);
        self::node($ami, 3, ['nama' => 'Auditor', 'route' => 'admin.ami.auditor.index', 'route_pattern' => 'admin.ami.auditor.*', 'icon' => 'bi-person-badge', 'permission' => 'auditor.view']);
        self::node($ami, 4, ['nama' => 'Jadwal Audit', 'route' => 'admin.ami.jadwal.index', 'route_pattern' => 'admin.ami.jadwal.*', 'icon' => 'bi-calendar-check', 'permission' => 'jadwal-ami.view']);
        self::node($ami, 5, ['nama' => 'Penugasan Saya', 'route' => 'admin.ami.penugasan.saya', 'route_pattern' => 'admin.ami.penugasan.*', 'icon' => 'bi-inbox', 'permission' => 'penugasan.respond']);
        self::node($ami, 6, ['nama' => 'Temuan', 'route' => 'admin.ami.temuan.index', 'route_pattern' => 'admin.ami.temuan.*', 'icon' => 'bi-exclamation-diamond', 'permission' => 'temuan.view']);

        $akr = self::node($ev, 2, ['nama' => 'Akreditasi', 'icon' => 'bi-award']);
        self::node($akr, 1, ['nama' => 'Data Akreditasi', 'route' => 'admin.akreditasi.index', 'route_pattern' => 'admin.akreditasi.*', 'icon' => 'bi-award', 'permission' => 'akreditasi.view']);
        self::node($akr, 2, ['nama' => 'Dashboard Akreditasi', 'route' => 'admin.akreditasi.dashboard', 'icon' => 'bi-speedometer2', 'permission' => 'akreditasi.view']);

        self::cs($ev, 3, 'Survey Kepuasan', 'survey-kepuasan', 'bi-emoji-smile');
        self::cs($ev, 4, 'Monev', 'monev', 'bi-binoculars');
        self::cs($ev, 5, 'Evaluasi Pembelajaran', 'evaluasi-pembelajaran', 'bi-easel');
        self::cs($ev, 6, 'Capaian Pembelajaran', 'capaian-pembelajaran', 'bi-graph-up-arrow');
        self::cs($ev, 7, 'Review RPS', 'review-rps', 'bi-file-earmark-text');

        $p3 = self::node($spmi, 4, ['nama' => 'P-3 Pengendalian', 'icon' => 'bi-3-circle']);
        self::node($p3, 1, [
            'nama' => 'Tindak Lanjut',
            'route' => 'admin.ami.tindak-lanjut.index',
            'route_pattern' => 'admin.ami.tindak-lanjut.*',
            'icon' => 'bi-clipboard-check',
            'permission' => 'tindak-lanjut.view',
            'badge_model' => 'App\\Models\\TindakLanjut',
            'badge_method' => 'pendingReview',
            'badge_class' => 'bg-warning',
        ]);
        self::cs($p3, 2, 'Monitoring', 'monitoring', 'bi-activity');

        $p4 = self::node($spmi, 5, ['nama' => 'P-4 Peningkatan', 'icon' => 'bi-4-circle']);
        self::cs($p4, 1, 'Rapat Tinjauan Manajemen', 'rapat-tinjauan-manajemen', 'bi-people');
        self::cs($p4, 2, 'Rencana Peningkatan Mutu', 'rencana-peningkatan-mutu', 'bi-graph-up');
        self::cs($p4, 3, 'Benchmarking', 'benchmarking', 'bi-bar-chart-steps');

        // ---- Laporan (top-level group) --------------------------------------
        $laporan = self::node(null, 4, ['nama' => 'Laporan', 'icon' => 'bi-bar-chart-line']);
        self::node($laporan, 1, ['nama' => 'Laporan AMI', 'route' => 'admin.laporan.index', 'route_pattern' => 'admin.laporan.*', 'icon' => 'bi-file-earmark-bar-graph', 'permission' => 'laporan-ami.view']);
        self::node($laporan, 2, ['nama' => 'Data Statistik', 'route' => 'admin.statistik.index', 'icon' => 'bi-table']);
        self::node($laporan, 3, ['nama' => 'Grafik', 'route' => 'admin.statistik.chart', 'icon' => 'bi-bar-chart']);

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
