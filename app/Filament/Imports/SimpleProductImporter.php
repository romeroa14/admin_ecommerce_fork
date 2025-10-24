<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SimpleProductImporter extends Importer
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

            ImportColumn::make('price')
                ->label('Precio')
                ->requiredMapping()
                ->rules(['required', 'numeric', 'min:0'])
                ->castStateUsing(fn (string $state): float => (float) $state),

            ImportColumn::make('stock')
                ->label('Stock')
                ->rules(['nullable', 'integer', 'min:0'])
                ->castStateUsing(fn (?string $state): ?int => $state ? (int) $state : 0),
        ];
    }

    public function resolveRecord(): ?Product
    {
        Log::info('SimpleProductImporter resolveRecord', [
            'data' => $this->data,
            'name' => $this->data['name'] ?? null,
            'price' => $this->data['price'] ?? null,
            'stock' => $this->data['stock'] ?? null,
        ]);

        // Siempre crear un nuevo producto
        return new Product();
    }

    protected function beforeSave(): void
    {
        Log::info('SimpleProductImporter beforeSave', [
            'data' => $this->data,
            'record_exists' => $this->record ? 'yes' : 'no',
            'record_id' => $this->record->id ?? 'new',
        ]);

        // Generar slug automáticamente
        if (!empty($this->record->name)) {
            $this->record->slug = Str::slug($this->record->name);
        }

        // Generar SKU automáticamente
        if (!empty($this->record->name)) {
            $baseSku = Str::upper(Str::slug($this->record->name, ''));
            $counter = 1;
            $sku = $baseSku;
            
            // Asegurar que el SKU sea único
            while (Product::where('sku', $sku)->where('id', '!=', $this->record->id ?? 0)->exists()) {
                $sku = $baseSku . '-' . $counter;
                $counter++;
            }
            
            $this->record->sku = $sku;
            Log::info('SimpleProductImporter: SKU generado', ['sku' => $sku]);
        }

        // Establecer valores por defecto para campos NOT NULL
        $this->record->status = 'active';
        $this->record->is_featured = false;
        $this->record->description = 'Descripción por defecto';
        $this->record->short_description = 'Descripción corta por defecto';
        $this->record->cost = 0;
        $this->record->track_inventory = true;
        $this->record->low_stock_threshold = 5;
        $this->record->category_id = 1; // Categoría por defecto
        $this->record->brand_id = 1;    // Marca por defecto

        Log::info('SimpleProductImporter: Valores por defecto asignados', [
            'category_id' => $this->record->category_id,
            'brand_id' => $this->record->brand_id,
            'status' => $this->record->status,
        ]);
    }

    protected function afterSave(): void
    {
        Log::info('SimpleProductImporter afterSave', [
            'record_id' => $this->record->id,
            'record_name' => $this->record->name,
            'record_slug' => $this->record->slug,
            'record_sku' => $this->record->sku,
            'record_price' => $this->record->price,
            'record_stock' => $this->record->stock,
            'action' => $this->record->wasRecentlyCreated ? 'created' : 'updated'
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importación simple completada. ';
        $body .= number_format($import->successful_rows) . ' productos procesados exitosamente.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' productos fallaron.';
        }

        return $body;
    }
}
