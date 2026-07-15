<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhookLog extends Model
{
    protected $fillable = ['gateway', 'event_id', 'transaction_reference', 'payload', 'status', 'error'];

    protected $casts = ['payload' => 'array'];
}
