<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferralTree extends Model
{
    protected $table = 'referral_tree';

    protected $fillable = [
        'user_id',
        'referrer_id',
        'referral_code',
        'commission_earned',
        'level',
    ];

    protected $casts = [
        'commission_earned' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(ReferralTree::class, 'referrer_id', 'user_id');
    }
}
