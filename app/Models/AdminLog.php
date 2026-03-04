<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'target_type',
        'target_id',
        'old_values',
        'new_values',
        'reason',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public static function logAction(User $admin, string $action, string $targetType, $targetId = null, ?array $oldValues = null, ?array $newValues = null, ?string $reason = null): void
    {
        self::create([
            'admin_id' => $admin->id,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'reason' => $reason,
            'ip_address' => request()->ip(),
        ]);
    }
}
