<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Administrator dengan akses penuh ke semua modul',
            ],
            [
                'name' => 'Auditor',
                'slug' => 'auditor',
                'description' => 'Auditor internal untuk pelaksanaan AMI',
            ],
            [
                'name' => 'Kepala Program Studi',
                'slug' => 'kaprodi',
                'description' => 'Kepala Program Studi yang di-audit',
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Hanya dapat melihat data tanpa edit',
            ],
            [
                'name' => 'Asesor',
                'slug' => 'asesor',
                'description' => 'Asesor eksternal, hanya dapat melihat dan mengunduh dokumen mutu yang telah disetujui',
            ],
            [
                'name' => 'Dosen',
                'slug' => 'dosen',
                'description' => 'Dosen: melihat data mutu, menindaklanjuti temuan AMI, dan mengunggah dokumen pendukung',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        // Create Permissions grouped by module
        $permissions = [
            // Dashboard
            ['name' => 'Lihat Dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard'],
            
            // Prodi Module
            ['name' => 'Lihat Prodi', 'slug' => 'prodi.view', 'module' => 'prodi'],
            ['name' => 'Tambah Prodi', 'slug' => 'prodi.create', 'module' => 'prodi'],
            ['name' => 'Edit Prodi', 'slug' => 'prodi.edit', 'module' => 'prodi'],
            ['name' => 'Hapus Prodi', 'slug' => 'prodi.delete', 'module' => 'prodi'],
            
            // Akreditasi Module
            ['name' => 'Lihat Akreditasi', 'slug' => 'akreditasi.view', 'module' => 'akreditasi'],
            ['name' => 'Tambah Akreditasi', 'slug' => 'akreditasi.create', 'module' => 'akreditasi'],
            ['name' => 'Edit Akreditasi', 'slug' => 'akreditasi.edit', 'module' => 'akreditasi'],
            ['name' => 'Hapus Akreditasi', 'slug' => 'akreditasi.delete', 'module' => 'akreditasi'],
            
            // Periode AMI
            ['name' => 'Lihat Periode AMI', 'slug' => 'periode-ami.view', 'module' => 'ami'],
            ['name' => 'Kelola Periode AMI', 'slug' => 'periode-ami.manage', 'module' => 'ami'],
            
            // Jadwal AMI
            ['name' => 'Lihat Jadwal AMI', 'slug' => 'jadwal-ami.view', 'module' => 'ami'],
            ['name' => 'Buat Jadwal AMI', 'slug' => 'jadwal-ami.create', 'module' => 'ami'],
            ['name' => 'Edit Jadwal AMI', 'slug' => 'jadwal-ami.edit', 'module' => 'ami'],
            ['name' => 'Hapus Jadwal AMI', 'slug' => 'jadwal-ami.delete', 'module' => 'ami'],
            
            // Auditor
            ['name' => 'Lihat Auditor', 'slug' => 'auditor.view', 'module' => 'ami'],
            ['name' => 'Kelola Auditor', 'slug' => 'auditor.manage', 'module' => 'ami'],
            
            // Penugasan
            ['name' => 'Lihat Penugasan', 'slug' => 'penugasan.view', 'module' => 'ami'],
            ['name' => 'Kelola Penugasan', 'slug' => 'penugasan.manage', 'module' => 'ami'],
            ['name' => 'Terima/Tolak Penugasan', 'slug' => 'penugasan.respond', 'module' => 'ami'],
            
            // Temuan
            ['name' => 'Lihat Temuan', 'slug' => 'temuan.view', 'module' => 'ami'],
            ['name' => 'Buat Temuan', 'slug' => 'temuan.create', 'module' => 'ami'],
            ['name' => 'Edit Temuan', 'slug' => 'temuan.edit', 'module' => 'ami'],
            ['name' => 'Hapus Temuan', 'slug' => 'temuan.delete', 'module' => 'ami'],
            ['name' => 'Verifikasi Temuan', 'slug' => 'temuan.verify', 'module' => 'ami'],
            
            // Tindak Lanjut
            ['name' => 'Lihat Tindak Lanjut', 'slug' => 'tindak-lanjut.view', 'module' => 'ami'],
            ['name' => 'Submit Tindak Lanjut', 'slug' => 'tindak-lanjut.submit', 'module' => 'ami'],
            ['name' => 'Review Tindak Lanjut', 'slug' => 'tindak-lanjut.review', 'module' => 'ami'],

            // Standar Mutu
            ['name' => 'Lihat Standar Mutu', 'slug' => 'standar-mutu.view', 'module' => 'ami'],
            ['name' => 'Kelola Standar Mutu', 'slug' => 'standar-mutu.manage', 'module' => 'ami'],

            // User Management
            ['name' => 'Lihat Users', 'slug' => 'users.view', 'module' => 'users'],
            ['name' => 'Kelola Users', 'slug' => 'users.manage', 'module' => 'users'],
            
            // Roles & Permissions
            ['name' => 'Kelola Roles', 'slug' => 'roles.manage', 'module' => 'settings'],
            ['name' => 'Kelola Permissions', 'slug' => 'permissions.manage', 'module' => 'settings'],
            
            // Reports
            ['name' => 'Lihat Laporan AMI', 'slug' => 'laporan-ami.view', 'module' => 'laporan'],
            ['name' => 'Export Laporan', 'slug' => 'laporan.export', 'module' => 'laporan'],

            // Buku Panduan
            ['name' => 'Lihat Buku Panduan', 'slug' => 'panduan.view', 'module' => 'panduan'],
            ['name' => 'Kelola Buku Panduan', 'slug' => 'panduan.manage', 'module' => 'panduan'],

            // Dokumen
            ['name' => 'Lihat Dokumen', 'slug' => 'dokumen.view', 'module' => 'dokumen'],
            ['name' => 'Unggah Dokumen', 'slug' => 'dokumen.upload', 'module' => 'dokumen'],
            ['name' => 'Kelola Dokumen', 'slug' => 'dokumen.manage', 'module' => 'dokumen'],

            // DKPS (Data Kinerja Program Studi)
            ['name' => 'Lihat DKPS', 'slug' => 'dkps.view', 'module' => 'dkps'],
            ['name' => 'Kelola DKPS', 'slug' => 'dkps.manage', 'module' => 'dkps'],

            // Audit Trail
            ['name' => 'Lihat Audit Trail', 'slug' => 'audit-trail.view', 'module' => 'ami'],

            // Evaluasi Diri & Lembar Kerja Audit (Fase 2)
            ['name' => 'Lihat Evaluasi Diri', 'slug' => 'evaluasi-diri.view', 'module' => 'ami'],
            ['name' => 'Isi Evaluasi Diri', 'slug' => 'evaluasi-diri.fill', 'module' => 'ami'],
            ['name' => 'Lihat Lembar Kerja Audit', 'slug' => 'lembar-audit.view', 'module' => 'ami'],
            ['name' => 'Isi Lembar Kerja Audit', 'slug' => 'lembar-audit.fill', 'module' => 'ami'],
            ['name' => 'Lihat Dokumen/Bukti Audit', 'slug' => 'bukti.view', 'module' => 'ami'],

            // RTM (Fase 3)
            ['name' => 'Lihat RTM', 'slug' => 'rtm.view', 'module' => 'ami'],
            ['name' => 'Kelola RTM', 'slug' => 'rtm.manage', 'module' => 'ami'],
        ];

        foreach ($permissions as $permData) {
            Permission::updateOrCreate(
                ['slug' => $permData['slug']],
                $permData
            );
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        // Backfill: accounts created before RBAC existed only have the legacy
        // 'admin' string column set, with no row in the roles pivot. The Users
        // edit form now requires a resolvable RBAC role (needed for admins to
        // edit their own account), so give those accounts the admin RBAC role too.
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            User::where('role', 'admin')
                ->doesntHave('roles')
                ->get()
                ->each(fn ($u) => $u->roles()->syncWithoutDetaching($adminRole));
        }
    }

    private function assignPermissionsToRoles(): void
    {
        // Admin gets all permissions
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync(Permission::pluck('id'));
        }

        // Auditor permissions
        $auditorRole = Role::where('slug', 'auditor')->first();
        if ($auditorRole) {
            $auditorPermissions = Permission::whereIn('slug', [
                'dashboard.view',
                'prodi.view',
                'akreditasi.view',
                'periode-ami.view',
                'jadwal-ami.view',
                'auditor.view',
                'penugasan.view',
                'penugasan.respond',
                'temuan.view',
                'temuan.create',
                'temuan.edit',
                'tindak-lanjut.view',
                'tindak-lanjut.review',
                'standar-mutu.view',
                'laporan-ami.view',
                'panduan.view',
                'dkps.view',
                'evaluasi-diri.view',
                'lembar-audit.view',
                'lembar-audit.fill',
                'bukti.view',
                'rtm.view',
            ])->pluck('id');
            $auditorRole->permissions()->sync($auditorPermissions);
        }

        // Kaprodi permissions
        $kaprodiRole = Role::where('slug', 'kaprodi')->first();
        if ($kaprodiRole) {
            $kaprodiPermissions = Permission::whereIn('slug', [
                'dashboard.view',
                'prodi.view',
                'akreditasi.view',
                'periode-ami.view',
                'jadwal-ami.view',
                'temuan.view',
                'tindak-lanjut.view',
                'tindak-lanjut.submit',
                'standar-mutu.view',
                'laporan-ami.view',
                'panduan.view',
                'dkps.view',
                'dkps.manage',
                'evaluasi-diri.view',
                'evaluasi-diri.fill',
                'lembar-audit.view',
                'bukti.view',
                'rtm.view',
            ])->pluck('id');
            $kaprodiRole->permissions()->sync($kaprodiPermissions);
        }

        // Viewer permissions
        $viewerRole = Role::where('slug', 'viewer')->first();
        if ($viewerRole) {
            $viewerPermissions = Permission::whereIn('slug', [
                'dashboard.view',
                'prodi.view',
                'akreditasi.view',
                'periode-ami.view',
                'jadwal-ami.view',
                'temuan.view',
                'tindak-lanjut.view',
                'standar-mutu.view',
                'laporan-ami.view',
                'panduan.view',
                'dkps.view',
                'evaluasi-diri.view',
                'lembar-audit.view',
                'bukti.view',
                'rtm.view',
            ])->pluck('id');
            $viewerRole->permissions()->sync($viewerPermissions);
        }

        // Asesor permissions - external assessor, read-only access to approved documents only
        $asesorRole = Role::where('slug', 'asesor')->first();
        if ($asesorRole) {
            $asesorPermissions = Permission::whereIn('slug', [
                'dashboard.view',
                'dokumen.view',
                'panduan.view',
            ])->pluck('id');
            $asesorRole->permissions()->sync($asesorPermissions);
        }

        // Dosen permissions - same baseline visibility as Viewer, plus following up
        // on AMI findings (submit tindak lanjut) and uploading supporting documents
        $dosenRole = Role::where('slug', 'dosen')->first();
        if ($dosenRole) {
            $dosenPermissions = Permission::whereIn('slug', [
                'dashboard.view',
                'prodi.view',
                'akreditasi.view',
                'periode-ami.view',
                'jadwal-ami.view',
                'temuan.view',
                'tindak-lanjut.view',
                'tindak-lanjut.submit',
                'standar-mutu.view',
                'laporan-ami.view',
                'panduan.view',
                'dokumen.view',
                'dokumen.upload',
                'dkps.view',
            ])->pluck('id');
            $dosenRole->permissions()->sync($dosenPermissions);
        }
    }
}
