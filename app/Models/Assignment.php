<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'lesson_id', 'title', 'instructions', 'max_points', 'due_date', 'allow_late_submission', 'late_penalty_percent', 'allowed_file_types', 'max_file_size_mb', 'is_published'];

    protected function casts(): array
    {
        return ['due_date' => 'datetime', 'allow_late_submission' => 'boolean', 'is_published' => 'boolean', 'allowed_file_types' => 'array'];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
