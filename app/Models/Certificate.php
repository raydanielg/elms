<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id', 'tenant_id', 'template_id', 'type', 'status', 'title',
        'certificate_number', 'verification_code', 'final_score',
        'pdf_path', 'social_image_path', 'signature_path', 'signature_title',
        'data_hash', 'metadata', 'issued_by', 'issued_at', 'revoked_at', 'revocation_reason'
    ];

    protected function casts(): array
    {
        return [
            'final_score' => 'decimal:2',
            'issued_at' => 'datetime',
            'revoked_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public function isValid(): bool
    {
        return $this->status === 'valid';
    }

    public function revoke(string $reason, int $revokedBy = null): void
    {
        $this->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revocation_reason' => $reason,
        ]);
    }

    public function generateDataHash(): string
    {
        $data = implode('|', [
            $this->user_id,
            $this->course_id,
            $this->certificate_number,
            $this->final_score,
            $this->issued_at?->timestamp,
        ]);
        return hash('sha256', $data);
    }
}

