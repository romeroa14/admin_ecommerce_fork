#!/bin/bash

# Script para procesar importaciones automáticamente
echo "Procesando importaciones..."

# Ejecutar el queue worker para procesar jobs de importación
php artisan queue:work --tries=3 --timeout=300 --stop-when-empty

echo "Importaciones procesadas."
