<?php

namespace App\Filament\Widgets;

use App\Models\SupportTicket\SupportTicket;
use Filament\Widgets\StatsOverviewWidget;

class SupportTicketsStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        return auth()->guard('admin')->check()
            && auth()->guard('admin')->user()->can('view_support_tickets');
    }

    public function getHeading(): ?string
    {
        return __('filament.dashboard.section_support_tickets');
    }

    public function getStats(): array
    {
        $total = SupportTicket::count();
        $open = SupportTicket::where('status', SupportTicket::STATUS_OPEN)->count();
        $inProgress = SupportTicket::where('status', SupportTicket::STATUS_IN_PROGRESS)->count();
        $closed = SupportTicket::where('status', SupportTicket::STATUS_CLOSED)->count();

        $cardBaseClasses = 'rounded-xl border border-slate-200 bg-white/90 dark:bg-gray-900/70 shadow-sm';

        return [
            StatsOverviewWidget\Stat::make(
                label: __('filament.dashboard.total_support_tickets'),
                value: $total,
            )
                ->description(__('filament.dashboard.support_tickets_total_help'))
                ->icon('heroicon-o-ticket')
                ->color('primary')
                ->extraAttributes([
                    'class' => $cardBaseClasses.' border-t-4 border-sky-400',
                ]),

            StatsOverviewWidget\Stat::make(
                label: __('filament.support_ticket.status_open'),
                value: $open,
            )
                ->description(__('filament.dashboard.support_tickets_open_help'))
                ->icon('heroicon-o-inbox')
                ->color('success')
                ->extraAttributes([
                    'class' => $cardBaseClasses.' border-t-4 border-emerald-400',
                ]),

            StatsOverviewWidget\Stat::make(
                label: __('filament.support_ticket.status_in_progress'),
                value: $inProgress,
            )
                ->description(__('filament.dashboard.support_tickets_in_progress_help'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->extraAttributes([
                    'class' => $cardBaseClasses.' border-t-4 border-amber-400',
                ]),

            StatsOverviewWidget\Stat::make(
                label: __('filament.support_ticket.status_closed'),
                value: $closed,
            )
                ->description(__('filament.dashboard.support_tickets_closed_help'))
                ->icon('heroicon-o-lock-closed')
                ->color('gray')
                ->extraAttributes([
                    'class' => $cardBaseClasses.' border-t-4 border-slate-400',
                ]),
        ];
    }
}
