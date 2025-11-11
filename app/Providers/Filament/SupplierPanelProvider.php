<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Navigation\NavigationItem;
use Filament\Support\Icons\Heroicon;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\RegisterPage;
use App\Filament\Pages\SupplierDashboard;

class SupplierPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('supplier')
            ->path('supplier')
            ->login()
            ->passwordReset()
            ->darkMode(false)
            ->colors([
                // 'primary' => Color::Amber,
            ])
            ->brandLogo(asset('project_files/SEPS Logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('project_files/SEPS Logo.png'))
            ->discoverResources(in: app_path('Filament/Supplier/Resources'), for: 'App\Filament\Supplier\Resources')
            ->discoverPages(in: app_path('Filament/Supplier/Pages'), for: 'App\Filament\Supplier\Pages')
            ->pages([
                SupplierDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Supplier/Widgets'), for: 'App\Filament\Supplier\Widgets')
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
                    ->sort(3)
                    ->icon(Heroicon::User)
                    ->label('User Profile')
                    ->url('/supplier/profile')
                    ->isActiveWhen(fn () => request()->routeIs('filament.supplier.auth.profile')),
            ])
            ->viteTheme('resources/css/filament/supplier/theme.css')
            ->profile(EditProfile::class, isSimple: false)
            ->login(Login::class)
            ->registration(RegisterPage::class)
            ->emailVerification()
            ->spa()
            ->unsavedChangesAlerts()
            ->topNavigation()
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable()
                    ->regenerableRecoveryCodes(false)
                    ->brandName('SEPS Davao del Sur'),
                EmailAuthentication::make(),
            ]);
    }

}
