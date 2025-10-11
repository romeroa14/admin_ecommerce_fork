<?php

namespace App\Enums;

enum NavigationGroup: string
{
    case CATALOG = 'Catálogo';
    case SALES = 'Ventas';
    case MARKETING = 'Marketing';
    case USERS = 'Usuarios';
    case ADMINISTRATION = 'Administración';
    
    public function getLabel(): string
    {
        return match($this) {
            self::CATALOG => '🛍️ Catálogo',
            self::SALES => '💰 Ventas',
            self::MARKETING => '📢 Marketing',
            self::USERS => '👥 Usuarios',
            self::ADMINISTRATION => '⚙️ Administración',
        };
    }
}