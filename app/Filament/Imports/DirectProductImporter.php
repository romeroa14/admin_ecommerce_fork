<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DirectProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getCsvDelimiter(): string
    {
        return ',';
    }

    public static function getCsvHeaderOffset(): int
    {
        return 0;
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nombre del Producto')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('description')
                ->label('Descripción')
                ->rules(['nullable', 'string']),

            ImportColumn::make('short_description')
                ->label('Descripción Corta')
                ->rules(['nullable', 'string', 'max:500']),

            ImportColumn::make('price')
                ->label('Precio')
                ->requiredMapping()
                ->rules(['required', 'numeric', 'min:0'])
                ->castStateUsing(fn (string $state): float => (float) $state),

            ImportColumn::make('compare_price')
                ->label('Precio de Comparación')
                ->rules(['nullable', 'numeric', 'min:0'])
                ->castStateUsing(fn (?string $state): ?float => $state ? (float) $state : null),

            ImportColumn::make('cost')
                ->label('Costo')
                ->rules(['nullable', 'numeric', 'min:0'])
                ->castStateUsing(fn (?string $state): ?float => $state ? (float) $state : null),

            ImportColumn::make('discount_percentage')
                ->label('Porcentaje de Descuento')
                ->rules(['nullable', 'numeric', 'min:0', 'max:100'])
                ->castStateUsing(fn (?string $state): ?float => $state ? (float) $state : null),

            ImportColumn::make('sku')
                ->label('SKU')
                ->rules(['nullable', 'string', 'max:100']),

            ImportColumn::make('stock')
                ->label('Stock')
                ->rules(['nullable', 'integer', 'min:0'])
                ->castStateUsing(fn (?string $state): ?int => $state ? (int) $state : 0),

            ImportColumn::make('low_stock_threshold')
                ->label('Umbral de Stock Bajo')
                ->rules(['nullable', 'integer', 'min:0'])
                ->castStateUsing(fn (?string $state): ?int => $state ? (int) $state : 5),

            ImportColumn::make('status')
                ->label('Estado')
                ->rules(['nullable', 'string', 'in:active,inactive,draft'])
                ->castStateUsing(fn (?string $state): string => $state ?: 'active'),

            ImportColumn::make('is_featured')
                ->label('Destacado')
                ->rules(['nullable', 'boolean'])
                ->castStateUsing(fn (?string $state): bool => $state === '1' || $state === 'true' || $state === 'yes'),

            ImportColumn::make('track_inventory')
                ->label('Rastrear Inventario')
                ->rules(['nullable', 'boolean'])
                ->castStateUsing(fn (?string $state): bool => $state !== '0' && $state !== 'false' && $state !== 'no'),

            ImportColumn::make('category_name')
                ->label('Categoría')
                ->rules(['nullable', 'string']),

            ImportColumn::make('brand_name')
                ->label('Marca')
                ->rules(['nullable', 'string']),

            ImportColumn::make('meta_title')
                ->label('Meta Título')
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('meta_description')
                ->label('Meta Descripción')
                ->rules(['nullable', 'string', 'max:500']),

            ImportColumn::make('meta_keywords')
                ->label('Meta Keywords')
                ->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        Log::info('DirectProductImporter resolveRecord', [
            'data' => $this->data
        ]);

        try {
            // Crear producto directamente
            $product = new Product();
            
            // Campos básicos
            $product->name = $this->data['name'] ?? 'Producto Sin Nombre';
            $product->description = $this->data['description'] ?? 'Descripción por defecto';
            $product->short_description = $this->data['short_description'] ?? 'Descripción corta';
            
            // Precios
            $product->price = $this->data['price'] ?? 0;
            $product->compare_price = $this->data['compare_price'] ?? null;
            $product->cost = $this->data['cost'] ?? 0;
            $product->discount_percentage = $this->data['discount_percentage'] ?? null;
            
            // SKU y Stock
            $product->sku = $this->data['sku'] ?? 'SKU-' . Str::upper(Str::slug($product->name, '')) . '-' . time();
            $product->stock = $this->data['stock'] ?? 0;
            $product->low_stock_threshold = $this->data['low_stock_threshold'] ?? 5;
            
            // Estado y configuración
            $product->status = $this->data['status'] ?? 'active';
            $product->is_featured = $this->data['is_featured'] ?? false;
            $product->track_inventory = $this->data['track_inventory'] ?? true;
            
            // SEO
            $product->meta_title = $this->data['meta_title'] ?? null;
            $product->meta_description = $this->data['meta_description'] ?? null;
            $product->meta_keywords = $this->data['meta_keywords'] ? explode(',', $this->data['meta_keywords']) : null;
            
            // Generar slug único
            $product->slug = Str::slug($product->name) . '-' . time();
            
            // Relaciones (se manejarán en beforeSave)
            $product->category_id = 1; // Por defecto
            $product->brand_id = 1;    // Por defecto

            Log::info('DirectProductImporter product created', [
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'sku' => $product->sku
            ]);

            return $product;
        } catch (\Exception $e) {
            Log::error('DirectProductImporter resolveRecord - ERROR', [
                'error' => $e->getMessage(),
                'data' => $this->data
            ]);
            return null;
        }
    }

    protected function beforeSave(): void
    {
        Log::info('DirectProductImporter beforeSave', [
            'record_name' => $this->record->name ?? 'NO_RECORD'
        ]);

        // Manejar categoría
        if (isset($this->data['category_name']) && !empty($this->data['category_name'])) {
            $category = \App\Models\Category::firstOrCreate(
                ['name' => $this->data['category_name']],
                ['slug' => Str::slug($this->data['category_name'])]
            );
            $this->record->category_id = $category->id;
        }

        // Manejar marca
        if (isset($this->data['brand_name']) && !empty($this->data['brand_name'])) {
            $brand = \App\Models\Brand::firstOrCreate(
                ['name' => $this->data['brand_name']],
                ['slug' => Str::slug($this->data['brand_name'])]
            );
            $this->record->brand_id = $brand->id;
        }
    }

    protected function afterSave(): void
    {
        Log::info('DirectProductImporter afterSave - PRODUCTO GUARDADO', [
            'record_id' => $this->record->id,
            'record_name' => $this->record->name,
            'was_created' => $this->record->wasRecentlyCreated
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Importación directa completada: ' . $import->successful_rows . ' productos importados exitosamente!';
    }
}
