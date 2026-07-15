<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InstructorLevel extends Model
{
    protected $fillable = [
        'level_number', 'name', 'slug', 'min_sales', 'min_rating',
        'max_refund_rate', 'commission_rate', 'payout_speed_days',
        'badge_icon', 'badge_color', 'perks', 'is_active', 'sort_order'
    ];

    protected $casts = [
        'min_rating' => 'decimal:2',
        'max_refund_rate' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'perks' => 'array',
        'is_active' => 'boolean',
    ];

    public function history() { return $this->hasMany(InstructorLevelHistory::class); }

    public static function forInstructor(User $instructor): ?self
    {
        $totalSales = Transaction::where('instructor_id', $instructor->id)
            ->where('type', 'course_purchase')
            ->where('status', 'completed')
            ->count();

        $courses = $instructor->courses()->where('status', 'published')->get();
        $avgRating = $courses->isNotEmpty() ? $courses->avg('average_rating') : 0;

        return static::where('is_active', true)
            ->where('min_sales', '<=', $totalSales)
            ->where('min_rating', '<=', $avgRating)
            ->orderBy('min_sales', 'desc')
            ->first();
    }

    public static function nextLevel(?self $current): ?self
    {
        if (!$current) return static::where('is_active', true)->orderBy('min_sales')->first();
        return static::where('is_active', true)
            ->where('min_sales', '>', $current->min_sales)
            ->orderBy('min_sales')
            ->first();
    }
}
