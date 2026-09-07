<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    use HasFactory;

    /** Maximum nesting depth supported by the editor and the sidebar. */
    public const MAX_DEPTH = 3;

    protected $fillable = [
        'nama',
        'icon',
        'route',
        'url',
        'route_pattern',
        'tipe',
        'lokasi',
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
     * Children eager-loaded recursively up to MAX_DEPTH (for the editor / sidebar).
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function activeChildrenRecursive()
    {
        return $this->children()->where('is_active', true)->with('activeChildrenRecursive');
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
     * Scope by placement: 'admin' (sidebar) or 'frontend' (public navbar).
     */
    public function scopeLokasi($query, string $lokasi)
    {
        return $query->where('lokasi', $lokasi);
    }

    /**
     * Admin sidebar tree (active only, MAX_DEPTH levels).
     */
    public static function getMenuTree()
    {
        return Cache::remember('admin_menu_tree_v2', 3600, function () {
            return self::active()
                ->lokasi('admin')
                ->root()
                ->with('activeChildrenRecursive')
                ->orderBy('urutan')
                ->get();
        });
    }

    /**
     * Public navbar tree (active only, MAX_DEPTH levels).
     */
    public static function getFrontendTree()
    {
        return Cache::remember('frontend_menu_tree_v1', 3600, function () {
            return self::active()
                ->lokasi('frontend')
                ->root()
                ->with('activeChildrenRecursive')
                ->orderBy('urutan')
                ->get();
        });
    }

    /**
     * Full tree (including inactive) for the menu editor.
     */
    public static function editorTree(string $lokasi = 'admin')
    {
        return self::lokasi($lokasi)
            ->root()
            ->with('childrenRecursive')
            ->orderBy('urutan')
            ->get();
    }

    /**
     * True when this item or any of its (loaded) descendants is the active route.
     */
    public function isBranchActive(): bool
    {
        if ($this->isActive()) {
            return true;
        }

        $kids = $this->relationLoaded('activeChildrenRecursive')
            ? $this->activeChildrenRecursive
            : $this->children;

        return $kids->contains(fn ($child) => $child->isBranchActive());
    }

    /**
     * Depth of this menu in the tree (1 = root).
     */
    public function depth(): int
    {
        $depth = 1;
        $node = $this;

        while ($node->parent_id) {
            $depth++;
            $node = $node->parent()->first();

            if (! $node || $depth > self::MAX_DEPTH + 1) {
                break;
            }
        }

        return $depth;
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
     * Whether $user should see this menu item. Admin always sees everything.
     * An item with no `permission` set is treated as admin-only (preserves
     * current behavior for menu rows nobody has explicitly opened up).
     */
    public function isVisibleTo(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (empty($this->permission)) {
            return false;
        }

        $required = array_filter(array_map('trim', explode(',', $this->permission)));

        return $user->hasAnyPermission($required);
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
        Cache::forget('admin_menu_tree_v2');
        Cache::forget('frontend_menu_tree_v1');
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
