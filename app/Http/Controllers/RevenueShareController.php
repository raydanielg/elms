<?php

namespace App\Http\Controllers;

use App\Models\RevenueShare;
use App\Models\User;
use Illuminate\Http\Request;

class RevenueShareController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) abort(403);
        $shares = RevenueShare::where('tenant_id', auth()->user()->tenant_id)
            ->with('teacher')->latest()->paginate(20);
        return view('revenue-shares.index', compact('shares'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) abort(403);
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'institution_percentage' => 'required|numeric|min:0|max:100',
            'teacher_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $existing = RevenueShare::where('teacher_id', $validated['teacher_id'])
            ->where('tenant_id', auth()->user()->tenant_id)->first();

        if ($existing) {
            $existing->update($validated);
            return response()->json(['message' => 'Revenue share updated']);
        }

        RevenueShare::create(array_merge($validated, ['tenant_id' => auth()->user()->tenant_id]));
        return response()->json(['message' => 'Revenue share created']);
    }

    public function update(Request $request, RevenueShare $revenueShare)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) abort(403);
        $revenueShare->update($request->only(['institution_percentage', 'teacher_percentage', 'is_active']));
        return response()->json(['message' => 'Revenue share updated']);
    }

    public function destroy(RevenueShare $revenueShare)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) abort(403);
        $revenueShare->delete();
        return response()->json(['message' => 'Revenue share removed']);
    }
}
