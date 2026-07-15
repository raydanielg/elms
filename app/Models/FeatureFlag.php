<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    protected $fillable = ['key', 'label', 'description', 'is_global_enabled', 'plan_ids', 'tenant_overrides'];

    protected $casts = [
        'is_global_enabled' => 'boolean',
        'plan_ids' => 'array',
        'tenant_overrides' => 'array',
    ];

    public static function isEnabled(string $key, ?Tenant $tenant = null): bool
    {
        $flag = static::where('key', $key)->first();
        if (!$flag) return true;

        if (!$flag->is_global_enabled) return false;

        if ($tenant) {
            $overrides = $flag->tenant_overrides ?? [];
            if (isset($overrides[$tenant->id])) {
                return (bool) $overrides[$tenant->id];
            }
            if ($tenant->plan_id && $flag->plan_ids) {
                return in_array($tenant->plan_id, $flag->plan_ids);
            }
        }

        return true;
    }
}
