<?php

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * AMI Fase 3: RTM, Analitik Mutu, Verifikasi RTL.
     * Daftarkan permission rtm.* dan bangun ulang menu sidebar SPMI.
     */
    public function up(): void
    {
        $perms = [
            'rtm.view' => 'Lihat RTM',
            'rtm.manage' => 'Kelola RTM',
        ];

        foreach ($perms as $slug => $name) {
            Permission::firstOrCreate(['slug' => $slug], ['name' => $name, 'module' => 'ami']);
        }

        $ids = Permission::whereIn('slug', array_keys($perms))->pluck('id');
        $viewId = Permission::where('slug', 'rtm.view')->value('id');

        if ($admin = Role::where('slug', 'admin')->first()) {
            $admin->permissions()->syncWithoutDetaching($ids);
        }
        foreach (['auditor', 'kaprodi', 'viewer'] as $slug) {
            if ($role = Role::where('slug', $slug)->first()) {
                $role->permissions()->syncWithoutDetaching([$viewId]);
            }
        }

        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Menu & permission data - reversal via re-running the seeders.
    }
};
