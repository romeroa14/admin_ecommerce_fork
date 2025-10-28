<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RevenueWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Ganancias totales
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        
        // Ganancias del mes actual
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        
        // Ganancias de la semana actual
        $weeklyRevenue = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->sum('amount');
        
        // Ganancias de hoy
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('payment_date', now()->toDateString())
            ->sum('amount');

        return [
            Stat::make('Ganancias Totales', '$' . number_format($totalRevenue, 2))
                ->description('Ingresos acumulados')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
                
            Stat::make('Ganancias del Mes', '$' . number_format($monthlyRevenue, 2))
                ->description('Ingresos de ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
                
            Stat::make('Ganancias de la Semana', '$' . number_format($weeklyRevenue, 2))
                ->description('Ingresos de esta semana')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
                
            Stat::make('Ganancias de Hoy', '$' . number_format($todayRevenue, 2))
                ->description('Ingresos de hoy')
                ->descriptionIcon('heroicon-m-sun')
                ->color('warning'),
        ];
    }
}