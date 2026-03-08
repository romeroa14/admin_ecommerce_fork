<?php

$dir = '/var/www/html/admin_ecommerce_fork/app/Filament/Resources';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), 'Form.php')) {
        $files[] = $file->getPathname();
    }
}

$dictionary = [
    'name' => 'Nombre',
    'slug' => 'Slug',
    'description' => 'Descripción',
    'short_description' => 'Descripción Corta',
    'is_active' => 'Activo',
    'status' => 'Estado',
    'sort_order' => 'Orden',
    'type' => 'Tipo',
    'color' => 'Color',
    'icon' => 'Icono',
    'email' => 'Correo Electrónico',
    'password' => 'Contraseña',
    'first_name' => 'Nombre',
    'last_name' => 'Apellido',
    'phone' => 'Teléfono',
    'company' => 'Empresa',
    'address_line_1' => 'Dirección 1',
    'address_line_2' => 'Dirección 2',
    'city' => 'Ciudad',
    'state' => 'Estado/Provincia',
    'country' => 'País',
    'postal_code' => 'Código Postal',
    'zip_code' => 'Código Postal',
    'base_price' => 'Precio Base',
    'price_per_kg' => 'Precio por Kg',
    'free_shipping_threshold' => 'Envío Gratis Desde',
    'estimated_days_min' => 'Días Estimados (Mínimo)',
    'estimated_days_max' => 'Días Estimados (Máximo)',
    'zones' => 'Zonas',
    'zone' => 'Zona',
    'weight_limits.min' => 'Peso Mín (kg)',
    'weight_limits.max' => 'Peso Máx (kg)',
    'code' => 'Código',
    'tracking_number' => 'Número de Seguimiento',
    'carrier' => 'Transportista',
    'service_level' => 'Nivel de Servicio',
    'shipping_cost' => 'Costo de Envío',
    'shipping_address' => 'Dirección de Envío',
    'notes' => 'Notas',
    'admin_notes' => 'Notas Internas',
    'shipped_at' => 'Fecha de Envío',
    'delivered_at' => 'Fecha de Entrega',
    'estimated_delivery' => 'Entrega Estimada',
    'processed_at' => 'Procesada el',
    'approved_at' => 'Aprobado el',
    'key' => 'Clave',
    'value' => 'Valor',
    'group' => 'Grupo',
    'is_public' => 'Es Público',
    'amount' => 'Monto',
    'reason' => 'Razón',
    'refund_number' => 'Número de Reembolso',
    'payment_id' => 'Pago Asociado',
    'order_id' => 'Pedido',
    'user_id' => 'Usuario',
    'product_id' => 'Producto',
    'category_id' => 'Categoría',
    'brand_id' => 'Marca',
    'variant_group_id' => 'Grupo de Variantes',
    'rating' => 'Calificación',
    'title' => 'Título',
    'comment' => 'Comentario',
    'images' => 'Imágenes',
    'is_verified_purchase' => 'Compra Verificada',
    'helpful_count' => 'Útil',
    'unhelpful_count' => 'No útil',
    'rate' => 'Tasa (%)',
    'is_compound' => 'Es Compuesto',
    'priority' => 'Prioridad',
    'default' => 'Por Defecto',
    'is_default' => 'Por Defecto',
];

foreach ($files as $file) {
    if (str_contains($file, 'InvoiceForm.php') || str_contains($file, 'AddressForm.php')) {
        continue;
    }

    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $newLines = [];
    $changed = false;

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        $newLines[] = $line;

        // Better regex that ignores whitespace and optional method chains
        if (preg_match('/([A-Z][A-Za-z0-9_]+)::make\s*\(\s*\'([a-zA-Z0-9_\.]+)\'\s*\)/', $line, $matches)) {
            $field = $matches[2];
            
            // Revisa si ya tiene un label
            if (str_contains($line, '->label(')) continue;
            
            $alreadyHasLabel = false;
            for ($j = 1; $j <= 5; $j++) {
                if (($i + $j) < count($lines)) {
                    if (str_contains($lines[$i + $j], '->label(')) {
                        $alreadyHasLabel = true;
                        break;
                    }
                    if (preg_match('/[A-Z][A-Za-z0-9_]+::make/', $lines[$i + $j])) break;
                }
            }

            if (!$alreadyHasLabel && isset($dictionary[$field])) {
                preg_match('/^(\s*)/', $line, $indentMatch);
                $indent = $indentMatch[1] . "    "; 
                $labelStr = $dictionary[$field];
                // Insert label line
                $newLines[] = $indent . "->label('{$labelStr}')";
                $changed = true;
            }
        }
    }

    if ($changed) {
        file_put_contents($file, implode("\n", $newLines));
        echo "Updated: $file\n";
    }
}
echo "Done.\n";
