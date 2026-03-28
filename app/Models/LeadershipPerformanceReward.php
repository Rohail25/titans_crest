<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadershipPerformanceReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'sponsor_user_id',
        'referred_user_id',
        'trigger_reference_id',
        'instant_commission_amount',
        'daily_bonus_amount',
        'total_days',
        'payouts_remaining',
        'next_payout_date',
        'next_payout_at',
        'last_paid_at',
        'completed_at',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'instant_commission_amount' => 'decimal:2',
        'daily_bonus_amount' => 'decimal:2',
        'total_days' => 'integer',
        'payouts_remaining' => 'integer',
        'next_payout_date' => 'date',
        'next_payout_at' => 'datetime',
        'last_paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_user_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
