<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'type', 'layout', 'design_config', 'background_image',
        'font_family', 'primary_color', 'secondary_color',
        'show_grade', 'show_qr_code', 'show_signature', 'show_logo',
        'is_active', 'version'
    ];

    protected $casts = [
        'design_config' => 'array',
        'show_grade' => 'boolean',
        'show_qr_code' => 'boolean',
        'show_signature' => 'boolean',
        'show_logo' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function certificates() { return $this->hasMany(Certificate::class, 'template_id'); }

    public static function forTenant(?int $tenantId): \Illuminate\Support\Collection
    {
        return static::where('is_active', true)
            ->where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })->orderBy('name')->get();
    }
}
