<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsLedger extends Model
{
    protected $table = 'points_ledger';

    protected $fillable = ['user_id', 'tenant_id', 'action', 'description', 'points', 'reference_type', 'reference_id'];

    public function user() { return $this->belongsTo(User::class); }
    public function tenant() { return $this->belongsTo(Tenant::class); }

    public static function totalForUser(int $userId): int
    {
        return static::where('user_id', $userId)->sum('points');
    }

    public static function award(int $userId, string $action, int $points, string $description = null, $reference = null): self
    {
        $entry = static::create([
            'user_id' => $userId,
            'tenant_id' => User::find($userId)?->tenant_id,
            'action' => $action,
            'description' => $description,
            'points' => $points,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference?->id,
        ]);

        return $entry;
    }
}
