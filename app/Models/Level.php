<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['tenant_id', 'level_number', 'name', 'min_xp', 'max_xp', 'icon', 'perks'];

    protected $casts = ['perks' => 'array'];

    public function tenant() { return $this->belongsTo(Tenant::class); }

    public static function forXp(int $xp, ?int $tenantId = null): ?self
    {
        return static::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
        })->where('min_xp', '<=', $xp)->orderBy('min_xp', 'desc')->first();
    }

    public static function nextLevel(int $xp, ?int $tenantId = null): ?self
    {
        return static::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
        })->where('min_xp', '>', $xp)->orderBy('min_xp')->first();
    }
}
