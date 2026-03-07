<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        // Get all settings as key-value pairs
        $settings = Pengaturan::getAll();
        return view('admin.pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);
        
        // Handle file uploads separately
        if ($request->hasFile('site_logo')) {
            $oldLogo = Pengaturan::get('site_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $settings['site_logo'] = $request->file('site_logo')->store('settings', 'public');
        }
        
        if ($request->hasFile('site_favicon')) {
            $oldFavicon = Pengaturan::get('site_favicon');
            if ($oldFavicon) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $settings['site_favicon'] = $request->file('site_favicon')->store('settings', 'public');
        }

        // Save all settings
        foreach ($settings as $key => $value) {
            if ($value !== null) {
                Pengaturan::set($key, $value);
            }
        }

        // Clear settings cache
        Cache::forget('site_settings');

        return redirect()->back()
            ->with('success', __('admin.settings_updated'));
    }

    public function template()
    {
        $settings = Pengaturan::getAll();
        return view('admin.pengaturan.template', compact('settings'));
    }

    public function updateTemplate(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            if ($value !== null) {
                Pengaturan::set($key, $value, 'text', 'template');
            }
        }

        Cache::forget('site_settings');

        return redirect()->route('admin.pengaturan.template')
            ->with('success', __('admin.template_updated'));
    }
}
