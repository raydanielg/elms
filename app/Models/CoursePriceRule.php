<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePriceRule extends Model
{
    protected $fillable = ['course_id', 'rule_type', 'rule_config', 'discount_value', 'discount_type', 'starts_at', 'ends_at', 'is_active'];

    protected $casts = [
        'rule_config' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function course() { return $this->belongsTo(Course::class); }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->ends_at && now()->gt($this->ends_at)) return false;
        return true;
    }

    public function calculatePrice(float $basePrice, ?int $enrollmentCount = null): float
    {
        if (!$this->isCurrentlyActive()) return $basePrice;

        if ($this->rule_type === 'scheduled_promotion') {
            if ($this->discount_type === 'percentage') {
                return max(0, $basePrice - ($basePrice * $this->discount_value / 100));
            }
            return max(0, $basePrice - $this->discount_value);
        }

        if ($this->rule_type === 'early_bird' && $enrollmentCount !== null) {
            $config = $this->rule_config ?? [];
            $limit = $config['first_n_students'] ?? 10;
            if ($enrollmentCount < $limit) {
                if ($this->discount_type === 'percentage') {
                    return max(0, $basePrice - ($basePrice * $this->discount_value / 100));
                }
                return max(0, $basePrice - $this->discount_value);
            }
        }

        return $basePrice;
    }
}
