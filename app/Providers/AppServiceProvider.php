<?php

namespace App\Providers;

use App\Models\Post\Post;
use App\Models\Post\PostCommentPreset;
use App\Models\User\Admin;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind LanguageSwitch as singleton to ensure configuration persists
        $this->app->singleton(LanguageSwitch::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('admin_post', fn (string $value) => Post::withTrashed()->whereKey($value)->firstOrFail());
        Route::bind('comment_preset', fn (string $value) => PostCommentPreset::withTrashed()->whereKey($value)->firstOrFail());

        // Super Admin (admin_type = super_admin) bypasses all permission checks everywhere:
        // Filament (canViewAny, canCreate, canEdit, …) and API (authorize, $user->can(…)).
        Gate::before(function ($user, $ability) {
            if ($user instanceof Admin && $user->admin_type === Admin::TYPE_SUPER_ADMIN) {
                return true;
            }

            return null;
        });

        // Configure LanguageSwitch - configure the singleton instance
        // This must be done in boot() and will persist when LanguageSwitch::boot() is called
        // Using default renderHook 'panels::global-search.after' - it works even when global search is disabled
        LanguageSwitch::make()
            ->locales(['ar', 'en'])  // Arabic and English
            ->labels([
                'en' => 'English',  // When current is Arabic, show "English" (target language)
                'ar' => 'العربية',     // When current is English, show "العربية" (target language)
            ])
            ->circular()  // Show only the opposite/inactive language directly (not dropdown)
            ->visible(insidePanels: true, outsidePanels: false)  // Only show in panels
            ->excludes([]);  // Don't exclude any panels - show in all panels
        // Using default renderHook 'panels::global-search.after' - works even with global search disabled
    }
}
