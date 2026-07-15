<?php

namespace App\Services;

use App\Models\PointsLedger;
use App\Models\Level;
use App\Models\User;

class PointsService
{
    public const ACTION_POINTS = [
        'lesson_completed' => 10,
        'quiz_passed' => 25,
        'quiz_perfect_score' => 50,
        'course_completed' => 100,
        'assignment_submitted' => 15,
        'assignment_on_time' => 10,
        'login_streak_7' => 30,
        'login_streak_30' => 100,
        'forum_post' => 5,
        'forum_reply' => 3,
        'badge_earned' => 0,
    ];

    public function award(int $userId, string $action, int $customPoints = null, string $description = null, $reference = null): PointsLedger
    {
        $points = $customPoints ?? (self::ACTION_POINTS[$action] ?? 0);
        return PointsLedger::award($userId, $action, $points, $description, $reference);
    }

    public function getTotal(int $userId): int
    {
        return PointsLedger::totalForUser($userId);
    }

    public function getLevel(int $userId): ?Level
    {
        $total = $this->getTotal($userId);
        $tenantId = User::find($userId)?->tenant_id;
        return Level::forXp($total, $tenantId);
    }

    public function getNextLevel(int $userId): ?Level
    {
        $total = $this->getTotal($userId);
        $tenantId = User::find($userId)?->tenant_id;
        return Level::nextLevel($total, $tenantId);
    }

    public function getProgressToNextLevel(int $userId): array
    {
        $total = $this->getTotal($userId);
        $current = $this->getLevel($userId);
        $next = $this->getNextLevel($userId);

        if (!$next) {
            return ['current_level' => $current, 'next_level' => null, 'progress' => 100, 'xp_remaining' => 0];
        }

        $minXp = $current?->min_xp ?? 0;
        $range = $next->min_xp - $minXp;
        $progress = $range > 0 ? round((($total - $minXp) / $range) * 100) : 0;

        return [
            'current_level' => $current,
            'next_level' => $next,
            'progress' => min(100, max(0, $progress)),
            'xp_remaining' => $next->min_xp - $total,
        ];
    }
}
