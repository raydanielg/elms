<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseVersion extends Model
{
    protected $fillable = ['course_id', 'version_number', 'content_snapshot', 'status', 'created_by', 'published_at'];

    protected $casts = [
        'content_snapshot' => 'array',
        'published_at' => 'datetime',
    ];

    public function course() { return $this->belongsTo(Course::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
