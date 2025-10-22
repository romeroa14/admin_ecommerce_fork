<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\CurrencySelector;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
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
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                'panels::topbar.start',
                fn (): string => '
                <div class="flex items-center space-x-3 px-4 py-2 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Moneda:</span>
                        <span class="text-lg font-bold text-primary-600 dark:text-primary-400">' . (\App\Helpers\CurrencyHelper::getCurrentCurrencySymbol()) . '</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">' . (\App\Helpers\CurrencyHelper::getCurrentCurrencyCode()) . '</span>
                    </div>
                    <form method="POST" action="' . route('currency.update') . '" class="flex items-center space-x-2">
                        ' . csrf_field() . '
                        <select name="currency_id" onchange="this.form.submit()" class="text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:text-white">
                            <option value="">Cambiar...</option>
                            ' . \App\Models\Currency::active()->ordered()->get()->map(function($currency) {
                                $current = \App\Helpers\CurrencyHelper::getCurrentCurrency();
                                $selected = $current && $current->id == $currency->id ? 'selected' : '';
                                return "<option value=\"{$currency->id}\" {$selected}>{$currency->symbol} {$currency->code}</option>";
                            })->join('') . '
                        </select>
                    </form>
                </div>'
            );
    }
}