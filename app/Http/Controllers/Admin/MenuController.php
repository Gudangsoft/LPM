<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('parent')
            ->orderBy('urutan')
            ->paginate(20);
        
        return view('admin.menu.index', compact('menus'));
    }

    public function create()
    {
        $parents = Menu::whereNull('parent_id')
            ->where('tipe', '!=', 'section')
            ->orderBy('urutan')
            ->get();
        
        return view('admin.menu.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'route_pattern' => 'nullable|string|max:255',
            'tipe' => 'required|in:link,section,divider',
            'parent_id' => 'nullable|exists:menus,id',
            'badge_model' => 'nullable|string|max:255',
            'badge_method' => 'nullable|string|max:100',
            'badge_class' => 'nullable|string|max:50',
            'urutan' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['badge_class'] = $validated['badge_class'] ?? 'bg-danger';

        Menu::create($validated);

        return redirect()->route('admin.menu.index')
            ->with('success', __('admin.menu_created'));
    }

    public function edit(Menu $menu)
    {
        $parents = Menu::whereNull('parent_id')
            ->where('tipe', '!=', 'section')
            ->where('id', '!=', $menu->id)
            ->orderBy('urutan')
            ->get();
        
        return view('admin.menu.edit', compact('menu', 'parents'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'route_pattern' => 'nullable|string|max:255',
            'tipe' => 'required|in:link,section,divider',
            'parent_id' => 'nullable|exists:menus,id',
            'badge_model' => 'nullable|string|max:255',
            'badge_method' => 'nullable|string|max:100',
            'badge_class' => 'nullable|string|max:50',
            'urutan' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $menu->update($validated);

        return redirect()->route('admin.menu.index')
            ->with('success', __('admin.menu_updated'));
    }

    public function destroy(Menu $menu)
    {
        // Delete children first
        $menu->children()->delete();
        $menu->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', __('admin.menu_deleted'));
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:menus,id',
            'orders.*.urutan' => 'required|integer|min:0',
        ]);

        foreach ($request->orders as $order) {
            Menu::where('id', $order['id'])->update(['urutan' => $order['urutan']]);
        }

        Menu::clearCache();

        return response()->json(['success' => true]);
    }
}
