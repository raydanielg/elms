<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsGateway extends Model
{
    protected $fillable = ['driver', 'label', 'is_active', 'priority', 'credentials', 'sender_id'];

    protected $casts = [
        'is_active' => 'boolean',
        'credentials' => 'array',
    ];

    public static function active(): ?self
    {
        return static::where('is_active', true)->orderBy('priority')->first();
    }
}
