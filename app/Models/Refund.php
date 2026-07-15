<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'transaction_id', 'user_id', 'amount', 'reason', 'status',
        'processed_by', 'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function transaction() { return $this->belongsTo(Transaction::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function processor() { return $this->belongsTo(User::class, 'processed_by'); }
}
