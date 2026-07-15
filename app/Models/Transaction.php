<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'user_id', 'course_id', 'instructor_id', 'type', 'amount',
        'currency', 'status', 'payment_method', 'transaction_reference',
        'gross_amount', 'commission_amount', 'commission_rate_applied',
        'gateway_fee', 'tax_amount', 'net_amount', 'metadata',
        'coupon_id', 'referral_id', 'instructor_level_at_sale'
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'gross_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'commission_rate_applied' => 'decimal:2',
            'gateway_fee' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
