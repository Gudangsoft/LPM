<?php

namespace App\Http\Controllers\Admin;

/**
 * Public website navbar menu (lokasi = frontend).
 */
class FrontendMenuController extends AbstractMenuController
{
    protected function location(): string
    {
        return 'frontend';
    }

    protected function routePrefix(): string
    {
        return 'admin.menu-web';
    }

    protected function pageTitle(): string
    {
        return __('admin.website_menu_editor');
    }
}
