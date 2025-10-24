<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class DebugProductImporter extends Importer
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
        ];
    }

    public function resolveRecord(): ?Product
    {
        Log::info('DebugProductImporter resolveRecord', [
            'data' => $this->data,
            'name' => $this->data['name'] ?? null,
            'price' => $this->data['price'] ?? null,
        ]);

        // Siempre crear un nuevo producto
        $product = new Product();
        $product->name = $this->data['name'] ?? 'Test Product';
        $product->price = $this->data['price'] ?? 0;
        $product->slug = 'test-' . time();
        $product->sku = 'TEST-' . time();
        $product->status = 'active';
        $product->description = 'Test product';
        $product->short_description = 'Test';
        $product->cost = 0;
        $product->stock = 0;
        $product->is_featured = false;
        $product->track_inventory = true;
        $product->low_stock_threshold = 5;
        $product->category_id = 1;
        $product->brand_id = 1;
        
        return $product;
    }

    protected function beforeSave(): void
    {
        Log::info('DebugProductImporter beforeSave', [
            'data' => $this->data,
            'record_exists' => $this->record ? 'yes' : 'no',
            'record_name' => $this->record->name ?? 'no name',
        ]);
        
        // No hacer nada, ya está todo configurado en resolveRecord
    }

    protected function afterSave(): void
    {
        Log::info('DebugProductImporter afterSave', [
            'record_id' => $this->record->id,
            'record_name' => $this->record->name,
            'action' => $this->record->wasRecentlyCreated ? 'created' : 'updated'
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Debug importación completada. ' . number_format($import->successful_rows) . ' productos procesados.';
    }
}
