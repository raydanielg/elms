<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = ['referrer_id', 'referred_id', 'course_id', 'referral_code', 'commission_amount', 'status'];

    public function referrer() { return $this->belongsTo(User::class, 'referrer_id'); }
    public function referred() { return $this->belongsTo(User::class, 'referred_id'); }
    public function course() { return $this->belongsTo(Course::class); }

    public static function generateCode(): string
    {
        return strtoupper(\Illuminate\Support\Str::random(10));
    }
}
