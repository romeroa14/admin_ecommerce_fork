<?php

$dir = '/var/www/html/admin_ecommerce_fork/app/Filament/Resources';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), 'Table.php')) {
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
    'tracking_number' => 'Nro. Seguimiento',
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
    'refund_number' => 'Nro. Reembolso',
    'payment_id' => 'Pago',
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
    'is_verified_purchase' => 'Verificado',
    'helpful_count' => 'Votos Útil',
    'unhelpful_count' => 'Votos No útil',
    'rate' => 'Tasa (%)',
    'is_compound' => 'Compuesto',
    'priority' => 'Prioridad',
    'default' => 'Por Defecto',
    'is_default' => 'Por Defecto',
    'created_at' => 'Creado El',
    'updated_at' => 'Actualizado El',
    'deleted_at' => 'Eliminado El',
    'price' => 'Precio',
    'stock' => 'Stock',
    'sku' => 'SKU',
    'user.name' => 'Usuario',
    'category.name' => 'Categoría',
    'brand.name' => 'Marca',
    'order.order_number' => 'Pedido Nro.',
    'product.name' => 'Producto',
    'id' => 'ID',
    'order_number' => 'Nro. Pedido',
    'total_amount' => 'Total',
    'subtotal' => 'Subtotal',
    'tax_amount' => 'Impuestos',
    'discount_amount' => 'Descuento',
    'grand_total' => 'Total General',
    'payment_method' => 'Método de Pago',
    'shipping_method' => 'Método de Envío',
    'payment_status' => 'Estado del Pago',
    'invoice_number' => 'Nro. Factura',
    'invoice_date' => 'Fecha de Factura',
    'due_date' => 'Fecha de Vencimiento',
    'currency' => 'Moneda',
    'exchange_rate' => 'Tasa de Cambio',
    'symbol' => 'Símbolo',
    'is_default' => 'Principal',
];

foreach ($files as $file) {
    if (str_contains($file, 'InvoiceTable.php') || str_contains($file, 'AddressTable.php')) {
        continue;
    }

    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $newLines = [];
    $changed = false;

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        $newLines[] = $line;

        // Matches Column::make('field')
        if (preg_match('/([A-Z][A-Za-z0-9_]+)::make\s*\(\s*\'([a-zA-Z0-9_\.]+)\'\s*\)$/', $line, $matches) || 
            preg_match('/([A-Z][A-Za-z0-9_]+)::make\s*\(\s*\'([a-zA-Z0-9_\.]+)\'\s*\),$/', $line, $matches)) {
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
                
                // Deal with commas correctly without breaking syntax
                $isComma = str_ends_with(trim($line), ',');
                if ($isComma) {
                    // Remove comma from current line
                    $newLines[count($newLines) - 1] = rtrim($line, ',');
                    $newLines[] = $indent . "->label('{$labelStr}'),";
                } else {
                    $newLines[] = $indent . "->label('{$labelStr}')";
                }
                
                $changed = true;
            }
        }
    }

    if ($changed) {
        file_put_contents($file, implode("\n", $newLines));
        echo "Updated table labels: $file\n";
    }
}
echo "Done.\n";
