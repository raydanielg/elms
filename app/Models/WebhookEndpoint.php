<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    protected $fillable = ['tenant_id', 'url', 'events', 'secret', 'is_active'];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function dispatches() { return $this->hasMany(WebhookDispatch::class); }

    public function listensTo(string $event): bool
    {
        if (!$this->events) return true;
        return in_array($event, $this->events) || in_array('*', $this->events);
    }
}
