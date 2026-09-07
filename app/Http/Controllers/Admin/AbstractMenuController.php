<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Shared CRUD + drag-and-drop tree logic for both the admin sidebar menu
 * (MenuController) and the public website navbar (FrontendMenuController).
 */
abstract class AbstractMenuController extends Controller
{
    /** 'admin' or 'frontend'. */
    abstract protected function location(): string;

    /** Route name prefix, e.g. 'admin.menu' or 'admin.menu-web'. */
    abstract protected function routePrefix(): string;

    /** Human label for the page heading. */
    abstract protected function pageTitle(): string;

    protected function route(string $action, $param = []): string
    {
        return route($this->routePrefix() . '.' . $action, $param);
    }

    protected function query()
    {
        return Menu::where('lokasi', $this->location());
    }

    // -- CRUD ----------------------------------------------------------------

    public function index()
    {
        $tree = Menu::editorTree($this->location());

        $parentOptions = $this->query()
            ->where('tipe', 'link')
            ->orderBy('urutan')
            ->get()
            ->filter(fn ($m) => $m->depth() < Menu::MAX_DEPTH)
            ->values();

        return view('admin.menu.index', [
            'tree' => $tree,
            'parentOptions' => $parentOptions,
            'routePrefix' => $this->routePrefix(),
            'pageTitle' => $this->pageTitle(),
            'location' => $this->location(),
        ]);
    }

    public function create()
    {
        return view('admin.menu.create', [
            'parents' => $this->parentCandidates(),
            'routePrefix' => $this->routePrefix(),
            'pageTitle' => $this->pageTitle(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateMenu($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['buka_tab'] = $request->boolean('buka_tab');
        $validated['badge_class'] = $validated['badge_class'] ?? 'bg-danger';
        $validated['lokasi'] = $this->location();

        if ($validated['tipe'] !== 'link') {
            $validated['parent_id'] = null;
        }

        $this->assertParentSameLocation($validated['parent_id'] ?? null);
        $this->assertWithinDepth($validated['parent_id'] ?? null);

        Menu::create($validated);

        return redirect()->to($this->route('index'))->with('success', __('admin.menu_created'));
    }

    public function edit(Menu $menu)
    {
        $this->assertOwned($menu);

        return view('admin.menu.edit', [
            'menu' => $menu,
            'parents' => $this->parentCandidates($menu),
            'routePrefix' => $this->routePrefix(),
            'pageTitle' => $this->pageTitle(),
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $this->assertOwned($menu);

        $validated = $this->validateMenu($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['buka_tab'] = $request->boolean('buka_tab');

        if ($validated['tipe'] !== 'link') {
            $validated['parent_id'] = null;
        }

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId && ($parentId === $menu->id || in_array($parentId, $this->descendantIds($menu), true))) {
            return back()->withInput()->withErrors(['parent_id' => __('admin.invalid_parent')]);
        }

        $this->assertParentSameLocation($parentId);
        $this->assertWithinDepth($parentId, $menu);

        $menu->update($validated);

        return redirect()->to($this->route('index'))->with('success', __('admin.menu_updated'));
    }

    public function destroy(Menu $menu)
    {
        $this->assertOwned($menu);

        $menu->children()->delete();
        $menu->delete();

        return redirect()->to($this->route('index'))->with('success', __('admin.menu_deleted'));
    }

    // -- Drag & drop -------------------------------------------------------

    public function updateTree(Request $request)
    {
        $data = $request->validate(['tree' => 'present|array']);

        $updates = [];
        $this->flatten($data['tree'], null, 1, $updates);

        $ownIds = $this->query()->pluck('id')->all();

        DB::transaction(function () use ($updates, $ownIds) {
            foreach ($updates as $u) {
                if (! in_array($u['id'], $ownIds, true)) {
                    continue; // never touch rows from the other location
                }
                Menu::whereKey($u['id'])->update([
                    'parent_id' => $u['parent_id'],
                    'urutan' => $u['urutan'],
                ]);
            }
        });

        Menu::clearCache();

        return response()->json(['success' => true, 'count' => count($updates)]);
    }

    public function quickUpdate(Request $request, Menu $menu)
    {
        $this->assertOwned($menu);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
            'buka_tab' => 'sometimes|boolean',
            'target' => 'sometimes|nullable|string|max:255',
        ]);

        // "target" is a single field the user types: a route name, a relative
        // path, or an absolute URL. Split it back into route / url columns.
        if ($request->has('target')) {
            $target = trim((string) $request->input('target'));

            if ($target === '') {
                $menu->route = null;
                $menu->url = null;
            } elseif (\Illuminate\Support\Str::startsWith($target, ['http://', 'https://', '/', '#', 'mailto:', 'tel:'])) {
                $menu->url = $target;
                $menu->route = null;
            } else {
                $menu->route = $target;
                $menu->url = null;
            }
        }

        $menu->fill(array_diff_key($validated, ['target' => null]));
        $menu->save();
        Menu::clearCache();

        return response()->json([
            'success' => true,
            'menu' => $menu->only(['id', 'nama', 'icon', 'is_active', 'buka_tab', 'route', 'url']),
        ]);
    }

    // -- helpers ---------------------------------------------------------------

    protected function assertOwned(Menu $menu): void
    {
        abort_unless($menu->lokasi === $this->location(), 404);
    }

    protected function assertParentSameLocation(?int $parentId): void
    {
        if ($parentId) {
            abort_unless(Menu::whereKey($parentId)->value('lokasi') === $this->location(), 422);
        }
    }

    protected function validateMenu(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'buka_tab' => 'boolean',
            'route_pattern' => 'nullable|string|max:255',
            'tipe' => 'required|in:link,section,divider',
            'parent_id' => 'nullable|exists:menus,id',
            'permission' => 'nullable|string|max:255',
            'badge_model' => 'nullable|string|max:255',
            'badge_method' => 'nullable|string|max:100',
            'badge_class' => 'nullable|string|max:50',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
    }

    protected function parentCandidates(?Menu $exclude = null)
    {
        return $this->query()
            ->where('tipe', 'link')
            ->when($exclude, fn ($q) => $q->where('id', '!=', $exclude->id))
            ->orderBy('urutan')
            ->get()
            ->filter(function ($m) use ($exclude) {
                if ($m->depth() >= Menu::MAX_DEPTH) {
                    return false;
                }
                if ($exclude && in_array($m->id, $this->descendantIds($exclude), true)) {
                    return false;
                }
                return true;
            })
            ->values();
    }

    protected function descendantIds(Menu $menu): array
    {
        $ids = [];
        foreach ($menu->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->descendantIds($child));
        }
        return $ids;
    }

    protected function assertWithinDepth(?int $parentId, ?Menu $moving = null): void
    {
        $parentDepth = $parentId ? (Menu::find($parentId)?->depth() ?? 0) : 0;
        $subtreeHeight = $moving ? $this->subtreeHeight($moving) : 1;

        abort_if($parentDepth + $subtreeHeight > Menu::MAX_DEPTH, 422, __('admin.max_depth_reached'));
    }

    protected function subtreeHeight(Menu $menu): int
    {
        if ($menu->children->isEmpty()) {
            return 1;
        }
        return 1 + $menu->children->max(fn ($c) => $this->subtreeHeight($c));
    }

    /**
     * @param  array<int, array{id: int|string, children?: array}>  $nodes
     * @param  array<int, array{id: int, parent_id: ?int, urutan: int}>  $out
     */
    protected function flatten(array $nodes, ?int $parentId, int $depth, array &$out): void
    {
        abort_if($depth > Menu::MAX_DEPTH, 422, __('admin.max_depth_reached'));

        $position = 1;
        foreach ($nodes as $node) {
            if (! isset($node['id']) || ! is_numeric($node['id'])) {
                continue;
            }
            $out[] = [
                'id' => (int) $node['id'],
                'parent_id' => $parentId,
                'urutan' => $position++,
            ];
            if (! empty($node['children']) && is_array($node['children'])) {
                $this->flatten($node['children'], (int) $node['id'], $depth + 1, $out);
            }
        }
    }
}
