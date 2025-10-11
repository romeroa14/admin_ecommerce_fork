<?php

namespace App\Enums;

enum NavigationGroup: string
{
    case CATALOG = '🛍️ Catálogo';
    case SALES = '💰 Ventas';
    case MARKETING = '📢 Marketing';
    case ADMINISTRATION = '⚙️ Administración';
    case USERS = '👥 Usuarios';
    case CONTENT = '📄 Contenido';
    case REPORTS = '📊 Reportes';
}