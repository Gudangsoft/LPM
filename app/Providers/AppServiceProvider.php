<?php

namespace App\Providers;

use App\Models\Pengaturan;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Both layouts load Bootstrap 5, so render pagination with Bootstrap markup
        // (default is Tailwind, whose unstyled SVG chevrons render full-screen here).
        Paginator::useBootstrapFive();

        // Share site settings with all views
        View::composer('*', function ($view) {
            $siteSettings = Cache::remember('site_settings', 3600, function () {
                if (Schema::hasTable('pengaturan')) {
                    return Pengaturan::getAll()->toArray();
                }
                return [];
            });
            
            $view->with('siteSettings', $siteSettings);
        });

        // Share notification bell data with the admin layout only
        View::composer('layouts.admin', function ($view) {
            if (auth()->check()) {
                $view->with('unreadNotificationsCount', auth()->user()->unreadNotifications()->count());
                $view->with('recentNotifications', auth()->user()->notifications()->latest()->take(8)->get());
            } else {
                $view->with('unreadNotificationsCount', 0);
                $view->with('recentNotifications', collect());
            }
        });
    }
}
