<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsOptOut extends Model
{
    protected $fillable = ['user_id', 'category'];

    public function user() { return $this->belongsTo(User::class); }

    public static function hasOptedOut(int $userId, string $category): bool
    {
        return static::where('user_id', $userId)->where('category', $category)->exists();
    }
}
