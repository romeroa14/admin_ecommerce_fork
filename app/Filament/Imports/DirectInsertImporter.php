<?php

namespace App\Filament\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DirectInsertImporter extends Importer
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
        // No crear el producto aquí, solo retornar null
        // El procesamiento se hará en beforeSave()
        return null;
    }

    protected function beforeSave(): void
    {
        // Procesar cada fila del CSV y hacer INSERT directo
        $this->processCsvRow();
    }

    protected function afterSave(): void
    {
        // No hacer nada aquí ya que el procesamiento se hizo en beforeSave
    }

    private function processCsvRow(): void
    {
        try {
            Log::info('DirectInsertImporter - Procesando fila', [
                'data' => $this->data
            ]);

            // Preparar datos para el INSERT
            $productData = $this->prepareProductData();
            
            // Hacer INSERT directo a la base de datos
            $productId = DB::table('products')->insertGetId($productData);
            
            Log::info('DirectInsertImporter - PRODUCTO INSERTADO DIRECTAMENTE', [
                'product_id' => $productId,
                'name' => $productData['name'],
                'sku' => $productData['sku']
            ]);

        } catch (\Exception $e) {
            Log::error('DirectInsertImporter - ERROR en INSERT directo', [
                'error' => $e->getMessage(),
                'data' => $this->data
            ]);
        }
    }

    private function prepareProductData(): array
    {
        // Manejar categoría
        $categoryId = 1; // Por defecto
        if (isset($this->data['category_name']) && !empty($this->data['category_name'])) {
            $category = Category::firstOrCreate(
                ['name' => $this->data['category_name']],
                ['slug' => Str::slug($this->data['category_name'])]
            );
            $categoryId = $category->id;
        }

        // Manejar marca
        $brandId = 1; // Por defecto
        if (isset($this->data['brand_name']) && !empty($this->data['brand_name'])) {
            $brand = Brand::firstOrCreate(
                ['name' => $this->data['brand_name']],
                ['slug' => Str::slug($this->data['brand_name'])]
            );
            $brandId = $brand->id;
        }

        // Preparar datos para INSERT
        $productData = [
            'name' => $this->data['name'] ?? 'Producto Sin Nombre',
            'slug' => Str::slug($this->data['name'] ?? 'producto') . '-' . time(),
            'sku' => $this->data['sku'] ?? 'SKU-' . Str::upper(Str::slug($this->data['name'] ?? 'producto', '')) . '-' . time(),
            'description' => $this->data['description'] ?? 'Descripción por defecto',
            'short_description' => $this->data['short_description'] ?? 'Descripción corta',
            'price' => $this->data['price'] ?? 0,
            'compare_price' => $this->data['compare_price'] ?? null,
            'cost' => $this->data['cost'] ?? 0,
            'discount_percentage' => $this->data['discount_percentage'] ?? null,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'stock' => $this->data['stock'] ?? 0,
            'low_stock_threshold' => $this->data['low_stock_threshold'] ?? 5,
            'track_inventory' => $this->data['track_inventory'] ?? true,
            'status' => $this->data['status'] ?? 'active',
            'is_featured' => $this->data['is_featured'] ?? false,
            'meta_title' => $this->data['meta_title'] ?? null,
            'meta_description' => $this->data['meta_description'] ?? null,
            'meta_keywords' => $this->data['meta_keywords'] ? json_encode(explode(',', $this->data['meta_keywords'])) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return $productData;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'INSERT DIRECTO COMPLETADO: ' . $import->successful_rows . ' productos insertados directamente en la base de datos!';
    }
}
