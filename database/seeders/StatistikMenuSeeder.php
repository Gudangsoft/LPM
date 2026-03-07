<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class StatistikMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = Menu::where('tipe', 'admin')->max('urutan') ?? 0;

        Menu::create([
            'nama' => 'Statistik',
            'url' => '#',
            'route' => null,
            'icon' => 'bi-graph-up',
            'tipe' => 'admin',
            'parent_id' => null,
            'urutan' => $order + 1,
            'is_active' => true,
        ]);

        $parentId = Menu::where('nama', 'Statistik')->where('tipe', 'admin')->first()->id;

        $subMenus = [
            ['nama' => 'Data Statistik', 'route' => 'admin.statistik.index', 'icon' => 'bi-table'],
            ['nama' => 'Grafik', 'route' => 'admin.statistik.chart', 'icon' => 'bi-bar-chart'],
        ];

        foreach ($subMenus as $index => $menu) {
            Menu::create([
                'nama' => $menu['nama'],
                'url' => '#',
                'route' => $menu['route'],
                'icon' => $menu['icon'],
                'tipe' => 'admin',
                'parent_id' => $parentId,
                'urutan' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->command->info('Statistik menu seeded successfully!');
    }
}
