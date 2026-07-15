<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseApproval extends Model
{
    protected $fillable = ['course_id', 'approval_type', 'status', 'requested_by', 'reviewed_by', 'review_notes', 'reviewed_at'];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function course() { return $this->belongsTo(Course::class); }
    public function requestedBy() { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewedBy() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
