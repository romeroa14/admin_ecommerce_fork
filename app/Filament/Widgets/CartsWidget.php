<?php

namespace App\Filament\Widgets;

use App\Models\Cart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CartsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Total de carritos creados
        $totalCarts = Cart::count();
        
        // Carritos activos (no convertidos en pedidos)
        $activeCarts = Cart::whereDoesntHave('order')->count();
        
        // Carritos convertidos en pedidos
        $convertedCarts = Cart::whereHas('order')->count();
        
        // Carritos creados hoy
        $todayCarts = Cart::whereDate('created_at', now()->toDateString())->count();

        return [
            Stat::make('Total de Carritos', number_format($totalCarts))
                ->description('Carritos creados')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),
                
            Stat::make('Carritos Activos', number_format($activeCarts))
                ->description('Carritos sin convertir')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Carritos Convertidos', number_format($convertedCarts))
                ->description('Convertidos en pedidos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Carritos de Hoy', number_format($todayCarts))
                ->description('Creados hoy')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}