<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.pengaturan', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:100',
            'hero_title' => 'required|string|max:200',
            'hero_subtitle' => 'required|string|max:500',
            'hero_tagline' => 'required|string|max:500',
            'site_icon' => 'nullable|image|mimes:png,jpg,jpeg,ico|max:1024',
            'site_favicon' => 'nullable|image|mimes:png,jpg,jpeg,ico|max:512',
        ]);

        Setting::setValue('site_name', $request->site_name);
        Setting::setValue('hero_title', $request->hero_title);
        Setting::setValue('hero_subtitle', $request->hero_subtitle);
        Setting::setValue('hero_tagline', $request->hero_tagline);

        if ($request->hasFile('site_icon')) {
            $icon = $request->file('site_icon');
            $path = $icon->storeAs('settings', 'icon.' . $icon->extension(), 'public');
            Setting::setValue('site_icon', 'settings/icon.' . $icon->extension());
        }

        if ($request->hasFile('site_favicon')) {
            $favicon = $request->file('site_favicon');
            $path = $favicon->storeAs('settings', 'favicon.' . $favicon->extension(), 'public');
            Setting::setValue('site_favicon', 'settings/favicon.' . $favicon->extension());
        }

        return redirect()->route('admin.pengaturan')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
