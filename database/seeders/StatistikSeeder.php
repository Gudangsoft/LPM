<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Statistik;

class StatistikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data penelitian
        $penelitianData = [
            ['tahun' => 2012, 'usulan' => 3, 'didanai' => 1],
            ['tahun' => 2013, 'usulan' => 3, 'didanai' => 2],
            ['tahun' => 2014, 'usulan' => 3, 'didanai' => 2],
            ['tahun' => 2015, 'usulan' => 5, 'didanai' => 3],
            ['tahun' => 2016, 'usulan' => 3, 'didanai' => 2],
            ['tahun' => 2017, 'usulan' => 4, 'didanai' => 3],
            ['tahun' => 2018, 'usulan' => 4, 'didanai' => 2],
            ['tahun' => 2019, 'usulan' => 5, 'didanai' => 3],
            ['tahun' => 2020, 'usulan' => 3, 'didanai' => 2],
            ['tahun' => 2021, 'usulan' => 3, 'didanai' => 1],
            ['tahun' => 2022, 'usulan' => 3, 'didanai' => 2],
            ['tahun' => 2023, 'usulan' => 4, 'didanai' => 1],
            ['tahun' => 2024, 'usulan' => 3, 'didanai' => 1],
            ['tahun' => 2025, 'usulan' => 1, 'didanai' => 0],
        ];

        foreach ($penelitianData as $data) {
            Statistik::create([
                'kategori' => Statistik::KATEGORI_PENELITIAN,
                'tahun' => $data['tahun'],
                'usulan' => $data['usulan'],
                'didanai' => $data['didanai'],
                'dana_usulan' => $data['usulan'] * 25000000, // Rp 25jt per usulan
                'dana_disetujui' => $data['didanai'] * 25000000,
            ]);
        }

        // Sample data pengabdian
        $pengabdianData = [
            ['tahun' => 2018, 'usulan' => 5, 'didanai' => 3],
            ['tahun' => 2019, 'usulan' => 7, 'didanai' => 4],
            ['tahun' => 2020, 'usulan' => 6, 'didanai' => 3],
            ['tahun' => 2021, 'usulan' => 8, 'didanai' => 5],
            ['tahun' => 2022, 'usulan' => 9, 'didanai' => 6],
            ['tahun' => 2023, 'usulan' => 10, 'didanai' => 5],
            ['tahun' => 2024, 'usulan' => 8, 'didanai' => 4],
            ['tahun' => 2025, 'usulan' => 3, 'didanai' => 1],
        ];

        foreach ($pengabdianData as $data) {
            Statistik::create([
                'kategori' => Statistik::KATEGORI_PENGABDIAN,
                'tahun' => $data['tahun'],
                'usulan' => $data['usulan'],
                'didanai' => $data['didanai'],
                'dana_usulan' => $data['usulan'] * 15000000, // Rp 15jt per usulan
                'dana_disetujui' => $data['didanai'] * 15000000,
            ]);
        }

        // Sample data publikasi
        $publikasiData = [
            ['tahun' => 2020, 'usulan' => 12, 'didanai' => 10],
            ['tahun' => 2021, 'usulan' => 15, 'didanai' => 12],
            ['tahun' => 2022, 'usulan' => 18, 'didanai' => 15],
            ['tahun' => 2023, 'usulan' => 22, 'didanai' => 18],
            ['tahun' => 2024, 'usulan' => 20, 'didanai' => 16],
            ['tahun' => 2025, 'usulan' => 5, 'didanai' => 3],
        ];

        foreach ($publikasiData as $data) {
            Statistik::create([
                'kategori' => Statistik::KATEGORI_PUBLIKASI,
                'tahun' => $data['tahun'],
                'usulan' => $data['usulan'],
                'didanai' => $data['didanai'],
                'dana_usulan' => $data['usulan'] * 5000000, // Rp 5jt per usulan
                'dana_disetujui' => $data['didanai'] * 5000000,
            ]);
        }

        $this->command->info('Statistik data seeded successfully!');
    }
}
