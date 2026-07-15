<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\BadgeRule;
use App\Models\StudentBadge;
use App\Services\RecognitionEngine;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $badges = Badge::where(function ($q) {
            $q->whereNull('tenant_id')->orWhere('tenant_id', auth()->user()->tenant_id);
        })->with('rules')->orderBy('category')->get();
        return view('badges.index', compact('badges'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'icon_image' => 'nullable|image|max:2048',
            'category' => 'required|in:milestone,skill,engagement,community,custom',
            'color' => 'nullable|string',
            'xp_reward' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('icon_image')) {
            $validated['icon_image'] = $request->file('icon_image')->store('badges', 'public');
        }

        $validated['tenant_id'] = auth()->user()->tenant_id;
        Badge::create($validated);
        return response()->json(['message' => 'Badge created']);
    }

    public function update(Request $request, Badge $badge)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $badge->update($request->only(['name', 'description', 'icon', 'color', 'xp_reward', 'is_active']));
        return response()->json(['message' => 'Badge updated']);
    }

    public function destroy(Badge $badge)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $badge->delete();
        return response()->json(['message' => 'Badge deleted']);
    }

    public function storeRule(Request $request, Badge $badge)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $validated = $request->validate([
            'trigger_event' => 'required|string',
            'conditions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $badge->rules()->create($validated);
        return response()->json(['message' => 'Badge rule created']);
    }

    public function destroyRule(Badge $badge, BadgeRule $rule)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $rule->delete();
        return response()->json(['message' => 'Badge rule deleted']);
    }

    public function trophyCase()
    {
        $badges = StudentBadge::where('user_id', auth()->id())->with('badge', 'course')->latest()->get();
        return view('badges.trophy-case', compact('badges'));
    }

    public function awardManual(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'solo_teacher'])) abort(403);
        $validated = $request->validate([
            'badge_id' => 'required|exists:badges,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $result = app(RecognitionEngine::class)->awardBadge($validated['badge_id'], $validated['user_id']);
        if (!$result) {
            return response()->json(['message' => 'Student already has this badge'], 422);
        }
        return response()->json(['message' => 'Badge awarded successfully']);
    }
}
