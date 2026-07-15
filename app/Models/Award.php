<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $fillable = [
        'tenant_id', 'title', 'description', 'type', 'awarded_to', 'awarded_by',
        'course_id', 'certificate_id', 'period', 'is_public'
    ];

    protected $casts = ['is_public' => 'boolean'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function recipient() { return $this->belongsTo(User::class, 'awarded_to'); }
    public function giver() { return $this->belongsTo(User::class, 'awarded_by'); }
    public function course() { return $this->belongsTo(Course::class); }
    public function certificate() { return $this->belongsTo(Certificate::class); }
}
