<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCredit extends Model
{
    protected $fillable = ['tenant_id', 'balance', 'total_purchased', 'total_used'];

    public function tenant() { return $this->belongsTo(Tenant::class); }

    public static function forTenant(?int $tenantId): self
    {
        return static::firstOrCreate(['tenant_id' => $tenantId], ['balance' => 0]);
    }

    public function deduct(int $amount): bool
    {
        if ($this->balance < $amount) return false;
        $this->decrement('balance', $amount);
        $this->increment('total_used', $amount);
        return true;
    }

    public function add(int $amount): void
    {
        $this->increment('balance', $amount);
        $this->increment('total_purchased', $amount);
    }
}
