<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Earning extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'reference_id',
        'amount',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'json',
    ];

    // Immutable - never update
    public function update(array $attributes = [], array $options = [])
    {
        throw new \Exception('Earnings entries are immutable and cannot be updated.');
    }

    public function delete()
    {
        throw new \Exception('Earnings entries are immutable and cannot be deleted.');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
