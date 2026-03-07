<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function index(Request $request)
    {
        $query = Kontak::latest();
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $kontaks = $query->paginate(20);
        
        return view('admin.kontak.index', compact('kontaks'));
    }

    public function show(Kontak $kontak)
    {
        $kontak->markAsRead();
        return view('admin.kontak.show', compact('kontak'));
    }

    public function reply(Request $request, Kontak $kontak)
    {
        $validated = $request->validate([
            'balasan' => 'required|string|max:5000',
        ]);

        $kontak->update([
            'balasan' => $validated['balasan'],
            'status' => 'replied',
        ]);

        // You can add email notification here

        return redirect()->route('admin.kontak.index')
            ->with('success', __('admin.contact_replied'));
    }

    public function destroy(Kontak $kontak)
    {
        $kontak->delete();

        return redirect()->route('admin.kontak.index')
            ->with('success', __('admin.contact_deleted'));
    }
}
