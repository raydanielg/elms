<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantTheme extends Model
{
    protected $fillable = ['tenant_id', 'primary_color', 'secondary_color', 'logo_path', 'favicon_path', 'font_family', 'custom_domain', 'email_sender_name', 'custom_css'];

    protected $casts = ['custom_css' => 'array'];

    public function tenant() { return $this->belongsTo(Tenant::class); }

    public static function forTenant(?int $tenantId): ?self
    {
        return $tenantId ? static::where('tenant_id', $tenantId)->first() : null;
    }

    public function toCssVariables(): string
    {
        return "--color-primary: {$this->primary_color};--color-secondary: {$this->secondary_color};"
            . ($this->font_family ? "--font-family: {$this->font_family};" : '');
    }
}
