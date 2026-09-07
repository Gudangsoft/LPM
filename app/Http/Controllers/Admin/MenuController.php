<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        $tree = Menu::editorTree();

        // Links eligible to be picked as a parent in the add modal: anything at
        // depth < MAX_DEPTH so a new child never exceeds the limit.
        $parentOptions = Menu::where('tipe', 'link')
            ->orderBy('urutan')
            ->get()
            ->filter(fn ($m) => $m->depth() < Menu::MAX_DEPTH)
            ->values();

        return view('admin.menu.index', compact('tree', 'parentOptions'));
    }

    public function create()
    {
        $parents = $this->parentCandidates();

        return view('admin.menu.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateMenu($request);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['badge_class'] = $validated['badge_class'] ?? 'bg-danger';

        if ($validated['tipe'] !== 'link') {
            $validated['parent_id'] = null; // sections / dividers are root-only
        }

        $this->assertWithinDepth($validated['parent_id'] ?? null);

        Menu::create($validated);

        return redirect()->route('admin.menu.index')
            ->with('success', __('admin.menu_created'));
    }

    public function edit(Menu $menu)
    {
        $parents = $this->parentCandidates($menu);

        return view('admin.menu.edit', compact('menu', 'parents'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $this->validateMenu($request);

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['tipe'] !== 'link') {
            $validated['parent_id'] = null;
        }

        // Guard: chosen parent may not be the item itself or one of its descendants.
        $parentId = $validated['parent_id'] ?? null;
        if ($parentId && ($parentId === $menu->id || in_array($parentId, $this->descendantIds($menu), true))) {
            return back()->withInput()->withErrors(['parent_id' => __('admin.invalid_parent')]);
        }

        $this->assertWithinDepth($parentId, $menu);

        $menu->update($validated);

        return redirect()->route('admin.menu.index')
            ->with('success', __('admin.menu_updated'));
    }

    public function destroy(Menu $menu)
    {
        $menu->children()->delete(); // cascade handles deeper levels via FK
        $menu->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', __('admin.menu_deleted'));
    }

    /**
     * Persist the whole nested order from the drag-and-drop editor.
     * Payload: tree = [{ id, children: [{ id, children: [...] }] }]
     */
    public function updateTree(Request $request)
    {
        $data = $request->validate([
            'tree' => 'present|array',
        ]);

        $updates = [];
        $this->flatten($data['tree'], null, 1, $updates);

        DB::transaction(function () use ($updates) {
            foreach ($updates as $u) {
                Menu::whereKey($u['id'])->update([
                    'parent_id' => $u['parent_id'],
                    'urutan' => $u['urutan'],
                ]);
            }
        });

        Menu::clearCache();

        return response()->json(['success' => true, 'count' => count($updates)]);
    }

    /**
     * Inline quick edit from the editor (name / icon / active).
     */
    public function quickUpdate(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
        ]);

        $menu->update($validated);
        Menu::clearCache();

        return response()->json([
            'success' => true,
            'menu' => $menu->only(['id', 'nama', 'icon', 'is_active']),
        ]);
    }

    // ---------------------------------------------------------------------

    private function validateMenu(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
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

    private function parentCandidates(?Menu $exclude = null)
    {
        return Menu::where('tipe', 'link')
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

    private function descendantIds(Menu $menu): array
    {
        $ids = [];

        foreach ($menu->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->descendantIds($child));
        }

        return $ids;
    }

    private function assertWithinDepth(?int $parentId, ?Menu $moving = null): void
    {
        $parentDepth = $parentId ? (Menu::find($parentId)?->depth() ?? 0) : 0;
        $subtreeHeight = $moving ? $this->subtreeHeight($moving) : 1;

        abort_if($parentDepth + $subtreeHeight > Menu::MAX_DEPTH, 422, __('admin.max_depth_reached'));
    }

    private function subtreeHeight(Menu $menu): int
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
    private function flatten(array $nodes, ?int $parentId, int $depth, array &$out): void
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
