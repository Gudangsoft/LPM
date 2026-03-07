<?php

namespace Database\Seeders;

use App\Models\Halaman;
use Illuminate\Database\Seeder;

class HalamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $halamans = [
            [
                'judul' => 'Profil LPM',
                'slug' => 'profil',
                'konten' => '<h3>Tentang Lembaga Penjaminan Mutu</h3>
<p>Lembaga Penjaminan Mutu (LPM) merupakan unit kerja di bawah Rektor yang bertugas untuk menjamin dan meningkatkan mutu pendidikan tinggi secara berkelanjutan. LPM bertanggung jawab atas pelaksanaan sistem penjaminan mutu internal (SPMI) di lingkungan universitas.</p>
<h4>Tugas dan Fungsi</h4>
<ul>
<li>Mengembangkan dan menerapkan sistem penjaminan mutu internal</li>
<li>Melakukan audit mutu internal secara berkala</li>
<li>Memfasilitasi akreditasi program studi dan institusi</li>
<li>Melakukan monitoring dan evaluasi terhadap standar mutu</li>
<li>Menyusun dan mengembangkan standar mutu akademik dan non-akademik</li>
</ul>',
                'konten_translations' => [
                    'en' => [
                        'judul' => 'LPM Profile',
                        'konten' => '<h3>About Quality Assurance Institute</h3>
<p>The Quality Assurance Institute (LPM) is a work unit under the Rector responsible for ensuring and continuously improving the quality of higher education. LPM is responsible for implementing the internal quality assurance system (SPMI) within the university.</p>
<h4>Duties and Functions</h4>
<ul>
<li>Develop and implement internal quality assurance systems</li>
<li>Conduct regular internal quality audits</li>
<li>Facilitate study program and institutional accreditation</li>
<li>Monitor and evaluate quality standards</li>
<li>Develop academic and non-academic quality standards</li>
</ul>',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'judul' => 'Visi dan Misi',
                'slug' => 'visi-misi',
                'konten' => '<h3>Visi</h3>
<p>Menjadi lembaga penjaminan mutu yang profesional, inovatif, dan berintegritas dalam mewujudkan pendidikan tinggi berkualitas dan berdaya saing global.</p>
<h3>Misi</h3>
<ol>
<li>Mengembangkan sistem penjaminan mutu yang komprehensif dan berkelanjutan</li>
<li>Membangun budaya mutu di seluruh unit kerja universitas</li>
<li>Meningkatkan kompetensi sumber daya manusia dalam bidang penjaminan mutu</li>
<li>Mendukung pencapaian akreditasi unggul program studi dan institusi</li>
<li>Melakukan benchmarking dengan perguruan tinggi nasional dan internasional</li>
</ol>',
                'konten_translations' => [
                    'en' => [
                        'judul' => 'Vision and Mission',
                        'konten' => '<h3>Vision</h3>
<p>To become a professional, innovative, and integrity-driven quality assurance institution in realizing quality higher education with global competitiveness.</p>
<h3>Mission</h3>
<ol>
<li>Develop comprehensive and sustainable quality assurance systems</li>
<li>Build a quality culture across all university work units</li>
<li>Improve human resource competence in quality assurance</li>
<li>Support excellent accreditation achievements for study programs and institutions</li>
<li>Conduct benchmarking with national and international universities</li>
</ol>',
                    ],
                ],
                'is_active' => true,
            ],
            [
                'judul' => 'Sistem Penjaminan Mutu',
                'slug' => 'sistem-penjaminan-mutu',
                'konten' => '<h3>Sistem Penjaminan Mutu Internal (SPMI)</h3>
<p>Sistem Penjaminan Mutu Internal (SPMI) adalah kegiatan sistemik penjaminan mutu pendidikan tinggi oleh setiap perguruan tinggi secara otonom untuk mengendalikan dan meningkatkan penyelenggaraan pendidikan tinggi secara berencana dan berkelanjutan.</p>
<h4>Komponen SPMI</h4>
<ul>
<li><strong>Penetapan Standar</strong> - Menetapkan standar mutu pendidikan</li>
<li><strong>Pelaksanaan Standar</strong> - Melaksanakan standar yang telah ditetapkan</li>
<li><strong>Evaluasi Pelaksanaan</strong> - Mengevaluasi pelaksanaan standar</li>
<li><strong>Pengendalian Pelaksanaan</strong> - Mengendalikan pelaksanaan standar</li>
<li><strong>Peningkatan Standar</strong> - Meningkatkan standar secara berkelanjutan</li>
</ul>',
                'konten_translations' => [
                    'en' => [
                        'judul' => 'Quality Assurance System',
                        'konten' => '<h3>Internal Quality Assurance System (SPMI)</h3>
<p>The Internal Quality Assurance System (SPMI) is a systematic quality assurance activity for higher education by each university autonomously to control and improve the implementation of higher education in a planned and sustainable manner.</p>
<h4>SPMI Components</h4>
<ul>
<li><strong>Standard Setting</strong> - Setting education quality standards</li>
<li><strong>Standard Implementation</strong> - Implementing established standards</li>
<li><strong>Implementation Evaluation</strong> - Evaluating standard implementation</li>
<li><strong>Implementation Control</strong> - Controlling standard implementation</li>
<li><strong>Standard Improvement</strong> - Continuously improving standards</li>
</ul>',
                    ],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($halamans as $halaman) {
            Halaman::create($halaman);
        }
    }
}