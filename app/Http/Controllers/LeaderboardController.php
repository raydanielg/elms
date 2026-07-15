<?php

namespace App\Http\Controllers;

use App\Services\LeaderboardService;
use App\Services\PointsService;
use App\Models\PointsLedger;
use App\Models\Course;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function courseLeaderboard(Request $request, Course $course)
    {
        $period = $request->get('period', 'all_time');
        $rankings = app(LeaderboardService::class)->courseLeaderboard($course->id, $period);
        return view('leaderboards.course', compact('rankings', 'course', 'period'));
    }

    public function tenantLeaderboard(Request $request)
    {
        $period = $request->get('period', 'all_time');
        $rankings = app(LeaderboardService::class)->tenantLeaderboard(auth()->user()->tenant_id, $period);
        return view('leaderboards.tenant', compact('rankings', 'period'));
    }

    public function pointsHistory()
    {
        $ledger = PointsLedger::where('user_id', auth()->id())->latest()->paginate(30);
        $progress = app(PointsService::class)->getProgressToNextLevel(auth()->id());
        $total = app(PointsService::class)->getTotal(auth()->id());
        return view('leaderboards.points', compact('ledger', 'progress', 'total'));
    }
}
