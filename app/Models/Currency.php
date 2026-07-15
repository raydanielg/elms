<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['code', 'name', 'symbol', 'locale', 'exchange_rate', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function active(): \Illuminate\Support\Collection
    {
        return static::where('is_active', true)->get();
    }

    public static function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) return $amount;
        $fromCurrency = static::where('code', $from)->first();
        $toCurrency = static::where('code', $to)->first();
        if (!$fromCurrency || !$toCurrency) return $amount;
        $usd = $amount / $fromCurrency->exchange_rate;
        return $usd * $toCurrency->exchange_rate;
    }

    public static function format(float $amount, string $code): string
    {
        $currency = static::where('code', $code)->first();
        $symbol = $currency?->symbol ?? $code;
        return $symbol . number_format($amount, 2);
    }
}
