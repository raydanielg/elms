<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTrigger extends Model
{
    protected $fillable = ['tenant_id', 'event', 'email_enabled', 'sms_enabled', 'in_app_enabled', 'push_enabled'];

    protected $casts = [
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
        'push_enabled' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }

    public static function forEvent(string $event, ?int $tenantId = null): ?self
    {
        return static::where('event', $event)
            ->where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->orderByRaw('tenant_id IS NULL')
            ->first();
    }
}
