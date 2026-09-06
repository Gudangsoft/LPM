<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', fn ($q) => $q->where('slug', $request->role));
        }

        $users = $query->paginate(15);
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $role = Role::findOrFail($validated['role_id']);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = $role->slug === 'admin' ? 'admin' : 'user';
        unset($validated['role_id']);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($validated);
        $user->roles()->sync([$role->id]);

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user_created'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $currentRoleId = $user->roles()->value('roles.id');

        return view('admin.users.edit', compact('user', 'roles', 'currentRoleId'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $role = Role::findOrFail($validated['role_id']);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['role'] = $role->slug === 'admin' ? 'admin' : 'user';
        unset($validated['role_id']);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        if ($user->id !== auth()->id()) {
            $user->roles()->sync([$role->id]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user_updated'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', __('admin.cannot_delete_self'));
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user_deleted'));
    }
}
