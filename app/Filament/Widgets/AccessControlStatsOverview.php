<?php

namespace App\Filament\Widgets;

use App\Models\User\Admin;
use App\Models\User\Permission;
use App\Models\User\Role;
use App\Models\User\User;
use Filament\Widgets\StatsOverviewWidget;

class AccessControlStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '60s';

    public function getHeading(): ?string
    {
        return __('filament.dashboard.section_access');
    }

    public function getStats(): array
    {
        $usersTotal = User::count();
        $usersActive = User::where('is_active', true)->count();

        $adminsTotal = Admin::count();
        $adminsActive = Admin::where('is_active', true)->count();

        $rolesTotal = Role::count();
        $permissionsTotal = Permission::count();

        $cardBaseClasses = 'rounded-xl border border-slate-200 bg-white/90 dark:bg-gray-900/70 shadow-sm';

        return [
            StatsOverviewWidget\Stat::make(
                label: __('filament.dashboard.total_users'),
                value: $usersTotal,
            )
                ->description(__('filament.dashboard.users_active_of_total', [
                    'active' => $usersActive,
                    'total' => $usersTotal,
                ]))
                ->icon('heroicon-o-user-group')
                ->color('secondary')
                ->extraAttributes([
                    'class' => $cardBaseClasses.' border-t-4 border-emerald-400',
                ]),

            StatsOverviewWidget\Stat::make(
                label: __('filament.dashboard.total_admins'),
                value: $adminsTotal,
            )
                ->description(__('filament.dashboard.admins_active_of_total', [
                    'active' => $adminsActive,
                    'total' => $adminsTotal,
                ]))
                ->icon('heroicon-o-shield-check')
                ->color('secondary')
                ->extraAttributes([
                    'class' => $cardBaseClasses.' border-t-4 border-sky-400',
                ]),

            StatsOverviewWidget\Stat::make(
                label: __('filament.dashboard.total_roles'),
                value: $rolesTotal,
            )
                ->description(__('filament.dashboard.total_roles_help'))
                ->icon('heroicon-o-key')
                ->color('warning')
                ->extraAttributes([
                    'class' => $cardBaseClasses.' border-t-4 border-amber-400',
                ]),

            StatsOverviewWidget\Stat::make(
                label: __('filament.dashboard.total_permissions'),
                value: $permissionsTotal,
            )
                ->description(__('filament.dashboard.total_permissions_help'))
                ->icon('heroicon-o-lock-closed')
                ->color('warning')
                ->extraAttributes([
                    'class' => $cardBaseClasses.' border-t-4 border-rose-400',
                ]),
        ];
    }
}
