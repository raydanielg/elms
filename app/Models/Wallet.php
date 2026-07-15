<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance', 'pending_balance', 'currency', 'total_earned', 'total_withdrawn'];

    protected function casts(): array
    {
        return ['balance' => 'decimal:2', 'pending_balance' => 'decimal:2', 'total_earned' => 'decimal:2', 'total_withdrawn' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function credit(float $amount, bool $pending = false): void
    {
        if ($pending) {
            $this->increment('pending_balance', $amount);
        } else {
            $this->increment('balance', $amount);
            $this->increment('total_earned', $amount);
        }
    }

    public function debit(float $amount): void
    {
        $this->decrement('balance', $amount);
        $this->increment('total_withdrawn', $amount);
    }

    public function movePendingToAvailable(float $amount): void
    {
        $this->decrement('pending_balance', $amount);
        $this->increment('balance', $amount);
        $this->increment('total_earned', $amount);
    }

    public static function getOrCreateForUser(int $userId): self
    {
        return static::firstOrCreate(['user_id' => $userId], ['balance' => 0, 'pending_balance' => 0, 'currency' => 'USD', 'total_earned' => 0, 'total_withdrawn' => 0]);
    }
}
