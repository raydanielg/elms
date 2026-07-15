<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationLog extends Model
{
    protected $fillable = ['verification_code', 'verifiable_type', 'verifiable_id', 'ip_address', 'user_agent', 'is_valid'];

    protected $casts = ['is_valid' => 'boolean'];

    public static function log(string $code, string $type, int $id, bool $isValid): void
    {
        static::create([
            'verification_code' => $code,
            'verifiable_type' => $type,
            'verifiable_id' => $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'is_valid' => $isValid,
        ]);
    }
}
