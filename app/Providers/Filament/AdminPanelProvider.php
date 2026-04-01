<?php

namespace App\Providers\Filament;

use App\Filament\Livewire\AdminDatabaseNotifications;
use App\Filament\Pages\Auth\Login;
use App\Filament\Resources\Admin\AdminResource;
use App\Filament\Resources\ContentPage\ContentPageResource;
use App\Filament\Resources\Notifications\NotificationBroadcastResource;
use App\Filament\Resources\Notifications\NotificationResource;
use App\Filament\Resources\Roles\RoleResource as ShieldRoleResource;
use App\Filament\Resources\SupportTicket\SupportTicketResource;
use App\Filament\Resources\User\UserResource;
use App\Filament\Widgets\AccessControlStatsOverview;
use App\Filament\Widgets\SupportTicketsStatsOverview;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Enums\DatabaseNotificationsPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        // Language switch is configured in AppServiceProvider::boot()
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('dashboard')
            ->login(Login::class)
            ->default()
            ->domain(null)
            ->brandName(__('filament.navigation.brand'))
            ->brandLogoHeight('3rem')
            ->globalSearch(false)
            ->favicon(asset('images/m3lesh-favicon.png'))
            ->colors([
                'primary' => '#5F5173',
                'secondary' => '#A38DBF',
            ])
            ->databaseNotifications(
                livewireComponent: AdminDatabaseNotifications::class,
                position: DatabaseNotificationsPosition::Topbar,
            )
            ->renderHook(
                'panels::styles.after',
                fn (): string => '<style>.fi-no-database .fi-no-notification-close-btn{display:none!important}</style>',
            )
            ->sidebarCollapsibleOnDesktop()
            ->navigation(function (NavigationBuilder $navigation): NavigationBuilder {
                return $navigation
                    ->items([
                        NavigationItem::make(__('filament.navigation.dashboard'))
                            ->icon('heroicon-o-home')
                            ->url(fn (): string => Dashboard::getUrl())
                            ->visible(fn (): bool => auth()->guard('admin')->user()?->can('view_dashboard') ?? false)
                            ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard')),
                    ])
                    ->groups([
                        NavigationGroup::make(__('filament.navigation.notifications_management'))
                            ->items([
                                ...(NotificationResource::shouldRegisterNavigation() ? NotificationResource::getNavigationItems() : []),
                                ...(NotificationBroadcastResource::shouldRegisterNavigation() ? NotificationBroadcastResource::getNavigationItems() : []),
                            ]),
                        NavigationGroup::make(__('filament.navigation.system_services'))
                            ->items([
                                ...(SupportTicketResource::shouldRegisterNavigation() ? SupportTicketResource::getNavigationItems() : []),
                                ...(ContentPageResource::shouldRegisterNavigation() ? ContentPageResource::getNavigationItems() : []),
                            ]),
                        NavigationGroup::make(__('filament.navigation.accounts_and_permissions'))
                            ->items([
                                ...(UserResource::shouldRegisterNavigation() ? UserResource::getNavigationItems() : []),
                                ...(AdminResource::shouldRegisterNavigation() ? AdminResource::getNavigationItems() : []),
                                ...(ShieldRoleResource::shouldRegisterNavigation() ? ShieldRoleResource::getNavigationItems() : []),
                            ]),
                    ]);
            })
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                SupportTicketsStatsOverview::class,
                AccessControlStatsOverview::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('admin');
    }
}
