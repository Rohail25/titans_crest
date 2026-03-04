<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price',
        'daily_profit_rate',
        'duration_days',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'daily_profit_rate' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }
}
