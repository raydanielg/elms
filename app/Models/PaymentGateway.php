<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = ['driver', 'label', 'category', 'is_active', 'priority', 'credentials', 'supported_currencies', 'supported_countries'];

    protected $casts = [
        'is_active' => 'boolean',
        'credentials' => 'array',
        'supported_currencies' => 'array',
        'supported_countries' => 'array',
    ];

    public static function active(): \Illuminate\Support\Collection
    {
        return static::where('is_active', true)->orderBy('priority')->get();
    }

    public static function forCurrency(string $currency): \Illuminate\Support\Collection
    {
        return static::where('is_active', true)
            ->where(function ($q) use ($currency) {
                $q->whereNull('supported_currencies')
                  ->orWhereJsonContains('supported_currencies', $currency)
                  ->orWhere('supported_currencies', '[]');
            })
            ->orderBy('priority')
            ->get();
    }
}
