<?php

namespace App\Services;

use App\Models\LeaderboardSnapshot;
use App\Models\PointsLedger;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class LeaderboardService
{
    public function courseLeaderboard(int $courseId, string $period = 'all_time', int $limit = 50): array
    {
        $query = Enrollment::where('course_id', $courseId)
            ->where('status', 'completed')
            ->with('user');

        if ($period === 'weekly') {
            $query->where('completed_at', '>=', now()->startOfWeek());
        } elseif ($period === 'monthly') {
            $query->where('completed_at', '>=', now()->startOfMonth());
        }

        $enrollments = $query->orderByDesc('final_score')->limit($limit)->get();

        return $enrollments->map(function ($enrollment, $index) {
            return [
                'rank' => $index + 1,
                'user_id' => $enrollment->user_id,
                'name' => $enrollment->user->name,
                'avatar' => $enrollment->user->avatar ?? null,
                'score' => (float)$enrollment->final_score,
                'completed_at' => $enrollment->completed_at?->format('M d, Y'),
            ];
        })->toArray();
    }

    public function tenantLeaderboard(?int $tenantId, string $period = 'all_time', int $limit = 50): array
    {
        $query = PointsLedger::query()
            ->select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit($limit);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($period === 'weekly') {
            $query->where('created_at', '>=', now()->startOfWeek());
        } elseif ($period === 'monthly') {
            $query->where('created_at', '>=', now()->startOfMonth());
        }

        $results = $query->get();

        return $results->map(function ($row, $index) {
            $user = User::find($row->user_id);
            return [
                'rank' => $index + 1,
                'user_id' => $row->user_id,
                'name' => $user?->name ?? 'Unknown',
                'avatar' => $user?->avatar ?? null,
                'points' => (int)$row->total_points,
            ];
        })->toArray();
    }

    public function snapshot(string $scope, ?int $courseId = null, ?int $tenantId = null, string $period = 'all_time'): LeaderboardSnapshot
    {
        $rankings = match ($scope) {
            'course' => $this->courseLeaderboard($courseId, $period),
            'tenant' => $this->tenantLeaderboard($tenantId, $period),
            default => $this->tenantLeaderboard(null, $period),
        };

        return LeaderboardSnapshot::create([
            'tenant_id' => $tenantId,
            'course_id' => $courseId,
            'scope' => $scope,
            'period' => $period,
            'rankings' => $rankings,
            'snapshot_at' => now(),
        ]);
    }
}
