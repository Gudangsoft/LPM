<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            'site_name' => 'Lembaga Penjaminan Mutu',
            'site_tagline' => 'Sistem Informasi Penjaminan Mutu Kampus',
            'site_description' => 'Website resmi Lembaga Penjaminan Mutu (LPM) yang bertugas untuk menjamin dan meningkatkan mutu pendidikan tinggi.',
            'footer_text' => 'Lembaga Penjaminan Mutu - Universitas',

            // Contact
            'contact_address' => 'Jl. Pendidikan No. 123, Kota Universitas, 12345',
            'contact_phone' => '(021) 1234567',
            'contact_fax' => '(021) 1234568',
            'contact_email' => 'lpm@universitas.ac.id',
            'contact_whatsapp' => '6281234567890',

            // Social Media
            'social_facebook' => 'https://facebook.com/lpmuniversitas',
            'social_instagram' => 'https://instagram.com/lpmuniversitas',
            'social_youtube' => 'https://youtube.com/@lpmuniversitas',

            // Template
            'primary_color' => '#0d6efd',
            'secondary_color' => '#6c757d',
            'header_bg_color' => '#ffffff',
            'footer_bg_color' => '#212529',
            'navbar_style' => 'light',
            'container_width' => 'container',
            'show_breadcrumb' => '1',
            'show_back_to_top' => '1',
            'show_slider' => '1',
            'show_welcome' => '1',
            'show_news' => '1',
            'show_announcement' => '1',
            'show_agenda' => '1',
            'show_gallery' => '1',
            
            // Dashboard
            'current_period' => 'Audit Mutu Internal (AMI) Semester Genap 2025/2026',
        ];

        foreach ($settings as $key => $value) {
            Pengaturan::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}