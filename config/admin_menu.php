<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Sidebar Menu Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the admin sidebar menu.
    | Each menu item can have:
    | - route: Named route
    | - icon: Bootstrap Icons class
    | - label: Translation key or static text
    | - route_pattern: Pattern to check for active state
    | - badge: Optional badge configuration (model, method for count)
    | - permission: Optional permission required (for future use)
    | - children: Optional submenu items
    |
    */

    'items' => [
        // Dashboard
        [
            'route' => 'admin.dashboard',
            'icon' => 'bi-speedometer2',
            'label' => 'Dashboard',
            'route_pattern' => 'admin.dashboard',
        ],

        // Content Section
        [
            'section' => 'admin.content',
        ],
        [
            'route' => 'admin.berita.index',
            'icon' => 'bi-newspaper',
            'label' => 'admin.news',
            'route_pattern' => 'admin.berita.*',
        ],
        [
            'route' => 'admin.kategori-berita.index',
            'icon' => 'bi-folder',
            'label' => 'admin.categories',
            'route_pattern' => 'admin.kategori-berita.*',
        ],
        [
            'route' => 'admin.pengumuman.index',
            'icon' => 'bi-megaphone',
            'label' => 'admin.announcements',
            'route_pattern' => 'admin.pengumuman.*',
        ],
        [
            'route' => 'admin.agenda.index',
            'icon' => 'bi-calendar-event',
            'label' => 'admin.agenda',
            'route_pattern' => 'admin.agenda.*',
        ],

        // Media Section
        [
            'section' => 'admin.media',
        ],
        [
            'route' => 'admin.galeri.index',
            'icon' => 'bi-images',
            'label' => 'admin.gallery',
            'route_pattern' => 'admin.galeri.*',
        ],
        [
            'route' => 'admin.dokumen.index',
            'icon' => 'bi-file-earmark-pdf',
            'label' => 'admin.documents',
            'route_pattern' => 'admin.dokumen.*',
        ],
        [
            'route' => 'admin.sliders.index',
            'icon' => 'bi-card-image',
            'label' => 'admin.sliders',
            'route_pattern' => 'admin.sliders.*',
        ],

        // Pages Section
        [
            'section' => 'admin.pages',
        ],
        [
            'route' => 'admin.halaman.index',
            'icon' => 'bi-file-text',
            'label' => 'admin.pages',
            'route_pattern' => 'admin.halaman.*',
        ],
        [
            'route' => 'admin.struktur-organisasi.index',
            'icon' => 'bi-diagram-3',
            'label' => 'admin.structure',
            'route_pattern' => 'admin.struktur-organisasi.*',
        ],

        // System Section
        [
            'section' => 'admin.system',
        ],
        [
            'route' => 'admin.kontak.index',
            'icon' => 'bi-envelope',
            'label' => 'admin.messages',
            'route_pattern' => 'admin.kontak.*',
            'badge' => [
                'model' => \App\Models\Kontak::class,
                'method' => 'unread',
                'class' => 'bg-danger',
            ],
        ],
        [
            'route' => 'admin.users.index',
            'icon' => 'bi-people',
            'label' => 'admin.users',
            'route_pattern' => 'admin.users.*',
        ],
        [
            'route' => 'admin.pengaturan.index',
            'icon' => 'bi-gear',
            'label' => 'admin.settings',
            'route_pattern' => 'admin.pengaturan.*',
        ],
    ],
];
