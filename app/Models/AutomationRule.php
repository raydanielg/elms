<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    protected $fillable = ['tenant_id', 'name', 'description', 'trigger_event', 'conditions', 'action_class', 'action_params', 'is_active'];

    protected $casts = [
        'conditions' => 'array',
        'action_params' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }

    public static function forEvent(string $event, ?int $tenantId = null): \Illuminate\Support\Collection
    {
        return static::where('trigger_event', $event)->where('is_active', true)
            ->where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->get();
    }

    public function passesConditions($model): bool
    {
        if (!$this->conditions) return true;
        foreach ($this->conditions as $field => $expected) {
            if (data_get($model, $field) != $expected) return false;
        }
        return true;
    }
}
