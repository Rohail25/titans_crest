<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'pending_balance',
        'suspicious_balance',
        'total_deposit',
        'total_earned',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'suspicious_balance' => 'decimal:2',
        'total_deposit' => 'decimal:2',
        'total_earned' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function earnings(): HasMany
    {
        return $this->user->earnings();
    }
}
