<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = ['wallet_id', 'user_id', 'amount', 'status', 'payout_method', 'payout_account', 'notes', 'processed_at', 'processed_by'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'processed_at' => 'datetime'];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
