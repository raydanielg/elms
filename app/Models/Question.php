<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['quiz_id', 'type', 'question_text', 'points', 'matching_pairs', 'order_items', 'correct_answer', 'explanation', 'sort_order'];

    protected function casts(): array
    {
        return ['matching_pairs' => 'array', 'order_items' => 'array'];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('sort_order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
