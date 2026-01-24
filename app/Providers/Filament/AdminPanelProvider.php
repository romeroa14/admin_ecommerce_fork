<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\RevenueWidget;
use App\Filament\Widgets\CartsWidget;
use App\Filament\Widgets\ActiveUsersWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationBuilder;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
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
            ->login()
            // IMPORTANT: Use separate guard for Filament to avoid session conflicts with frontend
            ->authGuard('filament')
            ->colors([
                'primary' => [
                    50 => '#fef2f2',
                    100 => '#fee2e2',
                    200 => '#fecaca',
                    300 => '#fca5a5',
                    400 => '#f87171',
                    500 => '#64181C', // Color principal vinotinto
                    600 => '#5a1619',
                    700 => '#4f1416',
                    800 => '#441213',
                    900 => '#391010',
                    950 => '#2e0d0e',
                ],
            ])
            ->brandLogo(asset('storage/Logos/equipocontainer.png'))
            ->brandLogoHeight('8.5rem')

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                RevenueWidget::class,
                CartsWidget::class,
                ActiveUsersWidget::class,
            ])

            ->navigationGroups([
                NavigationGroup::make('Catálogo')
                    ->collapsed(false),
                NavigationGroup::make('Ventas')
                    ->collapsed(false),
                NavigationGroup::make('Envios')
                    ->collapsed(false),
                NavigationGroup::make('Marketing')
                    ->collapsed(true),
                NavigationGroup::make('Usuarios')
                    ->collapsed(true),
                NavigationGroup::make('Configuraciones')
                    ->collapsed(true),
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
            ->renderHook(
                'panels::topbar.end',
                fn(): string => view('filament.widgets.currency-selector-inline', [
                    'currencies' => \App\Models\Currency::active()->ordered()->get(),
                    'currentCurrency' => \App\Helpers\CurrencyHelper::getCurrentCurrency(),
                ])->render()
            );

    }
}