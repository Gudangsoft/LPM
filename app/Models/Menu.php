<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'icon',
        'route',
        'url',
        'route_pattern',
        'tipe',
        'parent_id',
        'badge_model',
        'badge_method',
        'badge_class',
        'permission',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get parent menu
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Get children menus
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('urutan');
    }

    /**
     * Scope for active menus
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for root menus (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get all menus as tree structure for sidebar
     */
    public static function getMenuTree()
    {
        return Cache::remember('admin_menu_tree', 3600, function () {
            return self::active()
                ->root()
                ->with(['children' => function ($query) {
                    $query->active()->orderBy('urutan');
                }])
                ->orderBy('urutan')
                ->get();
        });
    }

    /**
     * Get badge count if configured
     */
    public function getBadgeCount()
    {
        if (empty($this->badge_model) || empty($this->badge_method)) {
            return 0;
        }

        try {
            $model = $this->badge_model;
            $method = $this->badge_method;
            
            if (class_exists($model) && method_exists($model, $method)) {
                return $model::$method()->count();
            }
        } catch (\Exception $e) {
            return 0;
        }

        return 0;
    }

    /**
     * Get the URL for this menu item
     */
    public function getUrl()
    {
        if ($this->route) {
            try {
                return route($this->route);
            } catch (\Exception $e) {
                return '#';
            }
        }

        return $this->url ?? '#';
    }

    /**
     * Check if this menu is currently active
     */
    public function isActive()
    {
        if ($this->route_pattern) {
            return request()->routeIs($this->route_pattern);
        }

        if ($this->route) {
            return request()->routeIs($this->route);
        }

        return false;
    }

    /**
     * Clear menu cache
     */
    public static function clearCache()
    {
        Cache::forget('admin_menu_tree');
    }

    /**
     * Boot method to clear cache on changes
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}
