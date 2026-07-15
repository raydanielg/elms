<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedCourse extends Model
{
    protected $fillable = ['course_id', 'placement', 'starts_at', 'ends_at', 'is_active'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function course() { return $this->belongsTo(Course::class); }

    public static function active(string $placement = 'marketplace_home'): \Illuminate\Support\Collection
    {
        return static::where('is_active', true)->where('placement', $placement)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->with('course')
            ->get();
    }
}
