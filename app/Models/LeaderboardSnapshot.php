<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderboardSnapshot extends Model
{
    protected $fillable = ['tenant_id', 'course_id', 'scope', 'period', 'rankings', 'snapshot_at'];

    protected $casts = [
        'rankings' => 'array',
        'snapshot_at' => 'datetime',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function course() { return $this->belongsTo(Course::class); }
}
