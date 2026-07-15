<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsBundle extends Model
{
    protected $fillable = ['name', 'credits', 'price', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
