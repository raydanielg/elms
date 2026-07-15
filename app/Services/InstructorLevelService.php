<?php

namespace App\Services;

use App\Models\InstructorLevel;
use App\Models\InstructorLevelHistory;
use App\Models\Transaction;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class InstructorLevelService
{
    public function recalculateLevel(User $instructor): ?InstructorLevel
    {
        $newLevel = InstructorLevel::forInstructor($instructor);
        $currentLevelId = $instructor->instructor_level_id ?? null;

        if ($newLevel && $newLevel->id !== $currentLevelId) {
            DB::transaction(function () use ($instructor, $newLevel, $currentLevelId) {
                InstructorLevelHistory::create([
                    'user_id' => $instructor->id,
                    'instructor_level_id' => $newLevel->id,
                    'previous_level_id' => $currentLevelId,
                    'reason' => 'auto_calculated',
                    'is_manual_override' => false,
                ]);

                $instructor->update(['instructor_level_id' => $newLevel->id]);

                $direction = $currentLevelId === null ? 'assigned' : 'promoted';
                app(NotificationService::class)->notify(
                    $instructor->id,
                    'success',
                    "Level Update — {$newLevel->name}!",
                    "You've been {$direction} to {$newLevel->name}. Your commission rate is now {$newLevel->commission_rate}%."
                );
            });
        }

        return $newLevel;
    }

    public function manualOverride(User $instructor, InstructorLevel $level, int $changedBy, string $reason = null): void
    {
        DB::transaction(function () use ($instructor, $level, $changedBy, $reason) {
            InstructorLevelHistory::create([
                'user_id' => $instructor->id,
                'instructor_level_id' => $level->id,
                'previous_level_id' => $instructor->instructor_level_id,
                'reason' => $reason ?? 'manual_override',
                'is_manual_override' => true,
                'changed_by' => $changedBy,
            ]);

            $instructor->update(['instructor_level_id' => $level->id]);
        });
    }

    public function getProgress(User $instructor): array
    {
        $current = InstructorLevel::find($instructor->instructor_level_id);
        if (!$current) {
            $current = InstructorLevel::where('is_active', true)->orderBy('min_sales')->first();
        }

        $next = InstructorLevel::nextLevel($current);

        $totalSales = Transaction::where('instructor_id', $instructor->id)
            ->where('type', 'course_purchase')
            ->where('status', 'completed')
            ->count();

        $avgRating = $instructor->courses()->where('status', 'published')->avg('rating') ?? 0;

        if (!$next) {
            return [
                'current' => $current,
                'next' => null,
                'total_sales' => $totalSales,
                'avg_rating' => round($avgRating, 2),
                'sales_remaining' => 0,
                'rating_remaining' => 0,
                'progress' => 100,
            ];
        }

        $salesRemaining = max(0, $next->min_sales - $totalSales);
        $ratingRemaining = max(0, (float)$next->min_rating - $avgRating);

        $salesProgress = $current ? min(100, round(($totalSales / max(1, $next->min_sales)) * 100)) : 0;

        return [
            'current' => $current,
            'next' => $next,
            'total_sales' => $totalSales,
            'avg_rating' => round($avgRating, 2),
            'sales_remaining' => $salesRemaining,
            'rating_remaining' => round($ratingRemaining, 2),
            'progress' => $salesProgress,
        ];
    }

    public function recalculateAll(): int
    {
        $instructors = User::whereIn('role', ['solo_teacher', 'teacher'])->get();
        $count = 0;
        foreach ($instructors as $instructor) {
            if ($this->recalculateLevel($instructor)) $count++;
        }
        return $count;
    }
}
