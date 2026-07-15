<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'code', 'discount_type', 'discount_value', 'max_discount', 'usage_limit', 'used_count', 'starts_at', 'expires_at', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'starts_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
