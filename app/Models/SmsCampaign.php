<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCampaign extends Model
{
    protected $fillable = ['tenant_id', 'user_id', 'title', 'message', 'recipient_filters', 'total_recipients', 'sent_count', 'delivered_count', 'failed_count', 'status', 'sent_at'];

    protected $casts = [
        'recipient_filters' => 'array',
        'sent_at' => 'datetime',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }
}
