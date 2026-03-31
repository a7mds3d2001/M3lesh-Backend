<?php

namespace App\Http\Requests\Device;

use App\Models\User\Admin;
use App\Models\User\Device;
use App\Models\User\User;
use Illuminate\Http\Request;

class StoreDeviceFromRequest
{
    public static function forUser(Request $request, User $user): void
    {
        self::store($request, $user, null);
    }

    public static function forAdmin(Request $request, Admin $admin): void
    {
        self::store($request, null, $admin);
    }

    protected static function store(Request $request, ?User $user, ?Admin $admin): void
    {
        $deviceId = $request->header('x-device-id');
        $deviceToken = $request->input('device_token');

        if (! $deviceId || ! $deviceToken) {
            return;
        }

        $attributes = [
            'platform' => $request->header('x-platform'),
            'manufacturer' => $request->header('x-device-manufacturer'),
            'model' => $request->header('x-device-model'),
            'os_version' => $request->header('x-os-version'),
            'app_version' => $request->header('x-app-version'),
            'device_token' => $deviceToken,
            'last_used_at' => now(),
        ];

        $query = Device::query()->where('device_id', $deviceId);

        if ($user) {
            $query->where('user_id', $user->id);
            $defaults = ['user_id' => $user->id];
        } else {
            $query->where('admin_id', $admin?->id);
            $defaults = ['admin_id' => $admin?->id];
        }

        /** @var Device|null $device */
        $device = $query->first();

        if ($device) {
            $device->fill($attributes)->save();

            return;
        }

        Device::create(array_merge($defaults, [
            'device_id' => $deviceId,
        ], $attributes));
    }
}
