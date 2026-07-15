<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['tenant_id', 'user_id', 'action', 'module', 'old_values', 'new_values', 'ip_address', 'user_agent'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }

    public static function log(string $action, string $module = null, array $old = null, array $new = null): void
    {
        static::create([
            'tenant_id' => auth()->user()?->tenant_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
