<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'owner_id', 'category_id', 'title', 'slug', 'description', 'thumbnail', 'preview_video', 'level', 'status', 'visibility', 'price', 'compare_price', 'language', 'drip_enabled', 'drip_days', 'has_certificate', 'duration_hours', 'sort_order', 'published_at'];

    protected function casts(): array
    {
        return ['drip_enabled' => 'boolean', 'has_certificate' => 'boolean', 'published_at' => 'datetime'];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function getEnrollmentCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getLessonsCountAttribute(): int
    {
        return $this->modules()->withCount('lessons')->get()->sum('lessons_count');
    }
}
