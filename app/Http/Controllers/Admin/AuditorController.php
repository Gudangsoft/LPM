<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuditorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:auditor.view', only: ['index', 'show']),
            new Middleware('permission:auditor.manage', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Auditor::with('user')->latest();
        
        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $auditors = $query->paginate(15);
        
        return view('admin.ami.auditor.index', compact('auditors'));
    }

    public function create()
    {
        // Get users who are not already auditors
        $users = User::active()
            ->whereDoesntHave('auditor')
            ->orderBy('name')
            ->get();
        
        return view('admin.ami.auditor.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:auditor,user_id',
            'nip' => 'nullable|string|max:50',
            'no_sertifikat' => 'nullable|string|max:100',
            'tanggal_sertifikat' => 'nullable|date',
            'masa_berlaku' => 'nullable|date|after:tanggal_sertifikat',
            'bidang_keahlian' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'catatan' => 'nullable|string',
        ]);

        $auditor = Auditor::create($validated);

        // Assign auditor role to user
        $user = User::find($validated['user_id']);
        $auditorRole = \App\Models\Role::where('slug', 'auditor')->first();
        if ($auditorRole && $user) {
            $user->assignRole($auditorRole);
        }

        return redirect()->route('admin.ami.auditor.index')
            ->with('success', 'Auditor berhasil ditambahkan.');
    }

    public function show(Auditor $auditor)
    {
        $auditor->load(['user', 'penugasan.jadwalAmi.prodi', 'temuan']);
        
        $stats = [
            'total_penugasan' => $auditor->penugasan()->count(),
            'penugasan_selesai' => $auditor->penugasan()->where('status', 'selesai')->count(),
            'total_temuan' => $auditor->temuan()->count(),
        ];
        
        return view('admin.ami.auditor.show', compact('auditor', 'stats'));
    }

    public function edit(Auditor $auditor)
    {
        return view('admin.ami.auditor.edit', compact('auditor'));
    }

    public function update(Request $request, Auditor $auditor)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:50',
            'no_sertifikat' => 'nullable|string|max:100',
            'tanggal_sertifikat' => 'nullable|date',
            'masa_berlaku' => 'nullable|date|after:tanggal_sertifikat',
            'bidang_keahlian' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'catatan' => 'nullable|string',
        ]);

        $auditor->update($validated);

        return redirect()->route('admin.ami.auditor.index')
            ->with('success', 'Data auditor berhasil diperbarui.');
    }

    public function destroy(Auditor $auditor)
    {
        if ($auditor->penugasan()->count() > 0) {
            return redirect()->route('admin.ami.auditor.index')
                ->with('error', 'Auditor tidak dapat dihapus karena masih memiliki penugasan.');
        }

        // Remove auditor role from user
        $user = $auditor->user;
        $auditorRole = \App\Models\Role::where('slug', 'auditor')->first();
        if ($auditorRole && $user) {
            $user->removeRole($auditorRole);
        }

        $auditor->delete();

        return redirect()->route('admin.ami.auditor.index')
            ->with('success', 'Auditor berhasil dihapus.');
    }
}
