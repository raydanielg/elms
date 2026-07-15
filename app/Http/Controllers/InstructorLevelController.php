<?php

namespace App\Http\Controllers;

use App\Models\InstructorLevel;
use App\Models\InstructorLevelHistory;
use App\Services\InstructorLevelService;
use App\Models\User;
use Illuminate\Http\Request;

class InstructorLevelController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $levels = InstructorLevel::orderBy('level_number')->get();
        return view('instructor-levels.index', compact('levels'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $validated = $request->validate([
            'level_number' => 'required|integer',
            'name' => 'required|string',
            'min_sales' => 'required|integer|min:0',
            'min_rating' => 'required|numeric|min:0|max:5',
            'max_refund_rate' => 'nullable|numeric|min:0|max:100',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'payout_speed_days' => 'required|integer|min:1',
            'badge_icon' => 'nullable|string',
            'badge_color' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        InstructorLevel::create($validated);
        return response()->json(['message' => 'Level created']);
    }

    public function update(Request $request, InstructorLevel $level)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $level->update($request->only([
            'name', 'min_sales', 'min_rating', 'max_refund_rate',
            'commission_rate', 'payout_speed_days', 'badge_icon', 'badge_color', 'is_active'
        ]));
        return response()->json(['message' => 'Level updated']);
    }

    public function destroy(InstructorLevel $level)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $level->delete();
        return response()->json(['message' => 'Level deleted']);
    }

    public function progress()
    {
        if (!auth()->user()->hasRole(['teacher', 'solo_teacher'])) abort(403);
        $progress = app(InstructorLevelService::class)->getProgress(auth()->user());
        $history = InstructorLevelHistory::where('user_id', auth()->id())->with('level')->latest()->limit(10)->get();
        return view('instructor-levels.progress', compact('progress', 'history'));
    }

    public function manualOverride(Request $request, User $instructor)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $validated = $request->validate([
            'instructor_level_id' => 'required|exists:instructor_levels,id',
            'reason' => 'nullable|string',
        ]);
        $level = InstructorLevel::find($validated['instructor_level_id']);
        app(InstructorLevelService::class)->manualOverride($instructor, $level, auth()->id(), $validated['reason'] ?? null);
        return response()->json(['message' => 'Level manually overridden']);
    }

    public function recalculateAll()
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $count = app(InstructorLevelService::class)->recalculateAll();
        return response()->json(['message' => "Recalculated levels for {$count} instructors"]);
    }
}
