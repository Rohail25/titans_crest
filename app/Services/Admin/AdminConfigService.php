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
