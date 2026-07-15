<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = ['tenant_id', 'user_id', 'name', 'key_hash', 'key_prefix', 'scopes', 'last_used_at', 'expires_at', 'is_active'];

    protected $casts = [
        'scopes' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }

    public static function generate(): array
    {
        $key = 'elms_' . \Illuminate\Support\Str::random(40);
        return [
            'key' => $key,
            'key_hash' => hash('sha256', $key),
            'key_prefix' => substr($key, 0, 12) . '...',
        ];
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        return true;
    }

    public function hasScope(string $scope): bool
    {
        if (!$this->scopes) return true;
        return in_array($scope, $this->scopes) || in_array('*', $this->scopes);
    }
}
