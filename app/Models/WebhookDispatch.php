<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDispatch extends Model
{
    protected $fillable = ['webhook_endpoint_id', 'event', 'payload', 'response_code', 'status'];

    protected $casts = ['payload' => 'array'];

    public function endpoint() { return $this->belongsTo(WebhookEndpoint::class, 'webhook_endpoint_id'); }
}
