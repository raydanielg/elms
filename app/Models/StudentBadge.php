<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentBadge extends Model
{
    protected $table = 'student_badges';

    protected $fillable = ['user_id', 'badge_id', 'course_id', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
    public function badge() { return $this->belongsTo(Badge::class); }
    public function course() { return $this->belongsTo(Course::class); }
}
