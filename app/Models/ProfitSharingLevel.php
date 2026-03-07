<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitSharingLevel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'level',
        'percentage',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level' => 'integer',
        'percentage' => 'decimal:2',
    ];
}
