<?php

namespace App\Providers\Filament;

use App\Enums\NavigationGroup;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->passwordReset()
            ->darkMode(false)
            ->renderHook(PanelsRenderHook::SCRIPTS_AFTER, fn (): string => view('filament.components.n8n-chat')->render())
            ->globalSearch(false)
            ->databaseNotifications()
            ->colors([
                // 'primary' => Color::Amber,
            ])
            ->brandLogo(asset('storage/project_files/SEPS Logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('project_files/SEPS Logo.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                // Dashboard::class,
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationItems([
                NavigationItem::make('Profile')
                    ->group(NavigationGroup::Accounts)
                    ->icon(Heroicon::OutlinedUser)
                    ->label('Profile')
                    ->url('/admin/profile')
                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.auth.profile')),
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->profile(EditProfile::class, isSimple: false)
            ->emailVerification()
            ->spa()
            ->unsavedChangesAlerts()
            ->maxContentWidth(Width::Full)
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable()
                    ->regenerableRecoveryCodes(false)
                    ->brandName('SEPS Davao del Sur'),
                EmailAuthentication::make(),
            ]);
        // ], isRequired: true);
    }
}
