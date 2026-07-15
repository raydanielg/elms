<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorLevelHistory extends Model
{
    protected $fillable = [
        'user_id', 'instructor_level_id', 'previous_level_id',
        'reason', 'is_manual_override', 'changed_by'
    ];

    protected $casts = ['is_manual_override' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function level() { return $this->belongsTo(InstructorLevel::class); }
    public function changedBy() { return $this->belongsTo(User::class, 'changed_by'); }
}
