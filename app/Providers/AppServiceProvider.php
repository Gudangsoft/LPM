<?php

namespace App\Providers;

use App\Models\Pengaturan;
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
    }
}
