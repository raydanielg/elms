<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = ['module_id', 'title', 'description', 'content_type', 'video_url', 'file_path', 'text_content', 'external_link', 'duration_minutes', 'is_preview', 'is_published', 'sort_order'];

    protected function casts(): array
    {
        return ['is_preview' => 'boolean', 'is_published' => 'boolean'];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }
}
