<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['label', 'route', 'icon', 'roles', 'parent_key', 'sort_order', 'is_visible'];

    protected $casts = [
        'roles' => 'array',
        'is_visible' => 'boolean',
    ];

    public static function forRole(string $role): \Illuminate\Support\Collection
    {
        return static::where('is_visible', true)
            ->where(function ($q) use ($role) {
                $q->whereNull('roles')->orWhereJsonContains('roles', $role);
            })
            ->orderBy('sort_order')
            ->get();
    }
}
