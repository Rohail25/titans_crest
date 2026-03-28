<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyPerformanceExcellenceReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'sponsor_user_id',
        'period_start',
        'period_end',
        'total_volume',
        'qualified_legs',
        'qualifying_tier_volume',
        'qualifying_tier_reward',
        'qualifying_tier_min_legs',
        'status',
        'paid_at',
        'leg_volumes',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_volume' => 'decimal:2',
        'qualifying_tier_volume' => 'decimal:2',
        'qualifying_tier_reward' => 'decimal:2',
        'qualified_legs' => 'integer',
        'qualifying_tier_min_legs' => 'integer',
        'paid_at' => 'datetime',
        'leg_volumes' => 'array',
        'metadata' => 'array',
    ];

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_user_id');
    }
}
