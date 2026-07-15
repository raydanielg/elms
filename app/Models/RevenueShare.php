<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueShare extends Model
{
    protected $fillable = [
        'tenant_id', 'teacher_id', 'institution_percentage', 'teacher_percentage', 'is_active'
    ];

    protected $casts = [
        'institution_percentage' => 'decimal:2',
        'teacher_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function teacher() { return $this->belongsTo(User::class, 'teacher_id'); }

    public static function forTeacher(int $teacherId, int $tenantId): ?self
    {
        return static::where('teacher_id', $teacherId)
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->first();
    }
}
