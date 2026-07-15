<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = ['tenant_id', 'form_type', 'field_name', 'field_label', 'field_type', 'is_required', 'options', 'sort_order', 'is_active'];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'options' => 'array',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function data() { return $this->hasMany(CustomFieldData::class); }

    public static function forForm(string $formType, ?int $tenantId = null): \Illuminate\Support\Collection
    {
        return static::where('form_type', $formType)->where('is_active', true)
            ->where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->orderBy('sort_order')
            ->get();
    }
}
