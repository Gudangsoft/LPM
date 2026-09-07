<?php

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * AMI Fase 1: daftarkan permission audit-trail.view (untuk admin) dan
     * bangun ulang menu sidebar SPMI dengan item Dashboard AMI, Instrumen Audit,
     * Monitoring, dan Audit Trail.
     */
    public function up(): void
    {
        $perm = Permission::firstOrCreate(
            ['slug' => 'audit-trail.view'],
            ['name' => 'Lihat Audit Trail', 'module' => 'ami']
        );

        $admin = Role::where('slug', 'admin')->first();
        if ($admin && ! $admin->permissions()->where('permissions.id', $perm->id)->exists()) {
            $admin->permissions()->attach($perm->id);
        }

        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Menu & permission data - reversal via re-running the seeders.
    }
};
