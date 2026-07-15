<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type', 'description', 'price_monthly', 'price_yearly', 'max_teachers', 'max_students', 'max_courses', 'storage_limit_gb', 'commission_rate', 'features', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['features' => 'array', 'is_active' => 'boolean'];
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
