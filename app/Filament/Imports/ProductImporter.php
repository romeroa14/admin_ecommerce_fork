<?php

namespace App\Filament\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Currency;
use App\Helpers\CurrencyHelper;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductImporter extends Importer
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

    public static function getOptionsFormComponents(): array
    {
        return [
            \Filament\Schemas\Components\Section::make('Información de Importación')
                ->description('Guía para importar productos correctamente')
                ->schema([
                    \Filament\Forms\Components\Placeholder::make('import_instructions')
                        ->label('')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-700">Importación por Categorías</span>
                                </div>
                                <div class="pl-4 space-y-2 text-sm text-gray-600">
                                    <p>• Selecciona una categoría específica para todos los productos del CSV</p>
                                    <p>• Selecciona una marca específica para todos los productos del CSV</p>
                                    <p>• El CSV debe contener solo datos de productos (sin columnas de categoría/marca)</p>
                                    <p>• Si necesitas importar productos de diferentes categorías, hazlo en importaciones separadas</p>
                                </div>
                            </div>
                        '))
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(false)
                ->columnSpanFull(),

            Select::make('category_id')
                ->label('Categoría')
                ->options(Category::all()->pluck('name', 'id'))
                ->required()
                ->searchable()
                ->preload()
                ->helperText('Selecciona la categoría para todos los productos que se importen'),

                Select::make('brand_id')
                    ->label('Marca')
                    ->options(Brand::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Selecciona la marca para todos los productos que se importen'),

                Select::make('currency_id')
                    ->label('Moneda de Importación')
                    ->options(Currency::active()->get()->pluck('name', 'id'))
                    ->default(CurrencyHelper::getCurrentCurrency()?->id)
                    ->searchable()
                    ->preload()
                    ->helperText('Selecciona la moneda para los precios en el CSV'),
        ];
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
        Log::info('ProductImporter resolveRecord', [
            'data' => $this->data
        ]);

        // Crear nuevo producto
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
        
        // Las relaciones se manejarán en beforeSave usando las opciones seleccionadas

        Log::info('ProductImporter product created', [
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'sku' => $product->sku
        ]);

        return $product;
    }

    protected function beforeSave(): void
    {
        Log::info('ProductImporter beforeSave', [
            'record_name' => $this->record->name ?? 'NO_RECORD',
            'options' => $this->options
        ]);

        // Usar las opciones seleccionadas en el formulario
        if (isset($this->options['category_id'])) {
            $this->record->category_id = $this->options['category_id'];
        }

        if (isset($this->options['brand_id'])) {
            $this->record->brand_id = $this->options['brand_id'];
        }

        // Manejar conversión de monedas si es necesario
        if (isset($this->options['currency_id'])) {
            $importCurrency = Currency::find($this->options['currency_id']);
            $currentCurrency = CurrencyHelper::getCurrentCurrency();
            
            if ($importCurrency && $currentCurrency && $importCurrency->id !== $currentCurrency->id) {
                // Convertir precios de la moneda de importación a la moneda actual
                $this->record->price = CurrencyHelper::convertAmount(
                    $this->record->price, 
                    $importCurrency, 
                    $currentCurrency
                );
                
                if ($this->record->compare_price) {
                    $this->record->compare_price = CurrencyHelper::convertAmount(
                        $this->record->compare_price, 
                        $importCurrency, 
                        $currentCurrency
                    );
                }
                
                if ($this->record->cost) {
                    $this->record->cost = CurrencyHelper::convertAmount(
                        $this->record->cost, 
                        $importCurrency, 
                        $currentCurrency
                    );
                }
                
                Log::info('ProductImporter: Precios convertidos', [
                    'from_currency' => $importCurrency->code,
                    'to_currency' => $currentCurrency->code,
                    'original_price' => $this->data['price'],
                    'converted_price' => $this->record->price
                ]);
            }
        }
    }

    protected function afterSave(): void
    {
        Log::info('ProductImporter afterSave - PRODUCTO GUARDADO', [
            'record_id' => $this->record->id,
            'record_name' => $this->record->name,
            'was_created' => $this->record->wasRecentlyCreated
        ]);
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return '🎉 Importación de Productos Completada';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $successful = number_format($import->successful_rows);
        $failed = number_format($import->getFailedRowsCount());
        $total = number_format($import->total_rows);

        $body = "¡Importación completada exitosamente! {$successful} de {$total} productos han sido agregados a tu catálogo.";

        if ($failed > 0) {
            $body .= " {$failed} productos no pudieron ser importados. Revisa los errores para más detalles.";
        } else {
            $body .= " Todos los productos se importaron sin errores.";
        }

        return $body;
    }
}
