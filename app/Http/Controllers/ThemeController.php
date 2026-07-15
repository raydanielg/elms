<?php

namespace App\Http\Controllers;

use App\Models\TenantTheme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function edit()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $theme = TenantTheme::firstOrCreate(['tenant_id' => auth()->user()->tenant_id]);
        return view('themes.edit', compact('theme'));
    }

    public function update(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $validated = $request->validate([
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'logo_path' => 'nullable|image|max:2048',
            'favicon_path' => 'nullable|image|max:512',
            'font_family' => 'nullable|string',
            'custom_domain' => 'nullable|string',
            'email_sender_name' => 'nullable|string',
        ]);

        if ($request->hasFile('logo_path')) {
            $validated['logo_path'] = $request->file('logo_path')->store('themes', 'public');
        }
        if ($request->hasFile('favicon_path')) {
            $validated['favicon_path'] = $request->file('favicon_path')->store('themes', 'public');
        }

        $theme = TenantTheme::firstOrCreate(['tenant_id' => auth()->user()->tenant_id]);
        $theme->update($validated);
        return response()->json(['message' => 'Theme updated successfully']);
    }
}
