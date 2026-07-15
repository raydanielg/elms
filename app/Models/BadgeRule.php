<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeRule extends Model
{
    protected $fillable = ['badge_id', 'trigger_event', 'conditions', 'is_active'];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
    ];

    public function badge() { return $this->belongsTo(Badge::class); }

    public function passesConditions($model): bool
    {
        if (!$this->conditions) return true;
        foreach ($this->conditions as $field => $expected) {
            $actual = data_get($model, $field);
            if (is_array($expected)) {
                if (!in_array($actual, $expected)) return false;
            } else {
                if ($actual != $expected) return false;
            }
        }
        return true;
    }
}
