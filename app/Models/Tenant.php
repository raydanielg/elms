<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type', 'plan_id', 'logo', 'domain', 'description', 'contact_email', 'contact_phone', 'address', 'status', 'trial_ends_at'];

    protected function casts(): array
    {
        return ['trial_ends_at' => 'datetime'];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
