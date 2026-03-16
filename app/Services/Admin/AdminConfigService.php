<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class AdminConfigService
{
    public static function getAll()
    {
        return Setting::all();
    }

    public static function getSetting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }

    public static function setSetting(string $key, string $value, ?string $description = null): Setting
    {
        return Setting::set($key, $value, $description);
    }

    public static function updateSettings(array $settings, User $admin): void
    {
        DB::transaction(function () use ($settings, $admin) {
            foreach ($settings as $key => $value) {
                $oldValue = Setting::get($key);
                Setting::set($key, $value);
                
                // Pass null as target_id for settings since they don't have numeric IDs
                AuditLogService::log($admin, 'update_setting', 'Setting', null, ['key' => $key, 'value' => $oldValue], ['key' => $key, 'value' => $value]);
            }
        });
    }

    public static function getPackageSettings()
    {
        return \App\Models\Package::all();
    }

    public static function getFilteredPackageSettings(array $filters = [], int $perPage = 25)
    {
        $allowedSorts = ['id', 'name', 'price', 'daily_profit_rate', 'duration_days', 'created_at'];
        $sort = in_array($filters['sort'] ?? 'created_at', $allowedSorts, true)
            ? ($filters['sort'] ?? 'created_at')
            : 'created_at';
        $direction = strtolower($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query = \App\Models\Package::query();

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where('name', 'like', "%{$search}%");
        }

        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        return $query->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function updatePackage(int $packageId, array $data, User $admin): void
    {
        DB::transaction(function () use ($packageId, $data, $admin) {
            $package = \App\Models\Package::findOrFail($packageId);
            $oldValues = $package->toArray();
            
            $package->update($data);
            
            AuditLogService::log($admin, 'update_package', 'Package', $packageId, $oldValues, $package->fresh()->toArray());
        });
    }
}
