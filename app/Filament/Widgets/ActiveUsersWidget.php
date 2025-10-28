<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActiveUsersWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Total de usuarios registrados
        $totalUsers = User::count();
        
        // Usuarios activos (que han hecho al menos un pedido)
        $activeUsers = User::whereHas('orders')->count();
        
        // Usuarios nuevos este mes
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Usuarios nuevos hoy
        $newUsersToday = User::whereDate('created_at', now()->toDateString())->count();

        return [
            Stat::make('Total de Usuarios', number_format($totalUsers))
                ->description('Usuarios registrados')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('Usuarios Activos', number_format($activeUsers))
                ->description('Con al menos un pedido')
                ->descriptionIcon('heroicon-m-user')
                ->color('success'),
                
            Stat::make('Nuevos Usuarios', number_format($newUsersThisMonth))
                ->description('Registrados este mes')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
                
            Stat::make('Usuarios de Hoy', number_format($newUsersToday))
                ->description('Registrados hoy')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
        ];
    }
}