<?php

namespace App\Http\Controllers;

use App\Models\FeatureFlag;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $flags = FeatureFlag::orderBy('label')->get();
        return view('feature-flags.index', compact('flags'));
    }

    public function update(Request $request, FeatureFlag $flag)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $flag->update($request->only(['is_global_enabled', 'plan_ids', 'tenant_overrides']));
        return response()->json(['message' => 'Feature flag updated successfully']);
    }

    public function toggleGlobal(FeatureFlag $flag)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $flag->toggle('is_global_enabled')->save();
        return response()->json(['message' => 'Feature flag toggled', 'is_global_enabled' => $flag->is_global_enabled]);
    }
}
