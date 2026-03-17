<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPackage extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'activated_at',
        'expires_at',
        'is_active',
        'total_deposit',
        'total_earned',
        'earning_cap',
        'package_status',
        'last_profit_time',
        'next_profit_time',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'total_deposit' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'earning_cap' => 'decimal:2',
        'last_profit_time' => 'datetime',
        'next_profit_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
