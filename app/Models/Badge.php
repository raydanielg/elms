<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Badge extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'slug', 'description', 'icon', 'icon_image',
        'category', 'color', 'xp_reward', 'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function rules() { return $this->hasMany(BadgeRule::class); }
    public function students() { return $this->belongsToMany(User::class, 'student_badges')->withPivot('course_id', 'metadata')->withTimestamps(); }

    protected static function booted(): void
    {
        static::creating(function (Badge $badge) {
            if (!$badge->slug) {
                $badge->slug = Str::slug($badge->name) . '-' . Str::random(6);
            }
        });
    }

    public static function forTenant(?int $tenantId): \Illuminate\Support\Collection
    {
        return static::where('is_active', true)
            ->where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })->orderBy('name')->get();
    }
}
