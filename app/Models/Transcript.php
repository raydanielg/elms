<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transcript extends Model
{
    protected $fillable = [
        'user_id', 'tenant_id', 'verification_code', 'pdf_path', 'status',
        'grading_scale', 'data_snapshot', 'data_hash', 'issued_at', 'expires_at'
    ];

    protected $casts = [
        'data_snapshot' => 'array',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function tenant() { return $this->belongsTo(Tenant::class); }

    public static function generateCode(): string
    {
        return 'TR-' . strtoupper(Str::random(12));
    }

    public function generateDataHash(): string
    {
        return hash('sha256', json_encode($this->data_snapshot));
    }
}
