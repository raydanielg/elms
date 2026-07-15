<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance', 'total_earned', 'total_withdrawn'];

    protected function casts(): array
    {
        return ['balance' => 'decimal:2', 'total_earned' => 'decimal:2', 'total_withdrawn' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }
}
