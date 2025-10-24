<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class UltraSimpleImporter extends Importer
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
                ->label('Nombre')
                ->requiredMapping()
                ->rules(['required', 'string']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        Log::info('UltraSimpleImporter resolveRecord - INICIO', [
            'data' => $this->data,
            'name' => $this->data['name'] ?? 'NO_NAME'
        ]);

        $product = new Product();
        $product->name = $this->data['name'] ?? 'Producto Test';
        $product->slug = 'test-' . time() . '-' . rand(1000, 9999);
        $product->sku = 'TEST-' . time() . '-' . rand(1000, 9999);
        $product->price = 99.99;
        $product->status = 'active';
        $product->description = 'Producto de prueba';
        $product->short_description = 'Test';
        $product->cost = 0;
        $product->stock = 10;
        $product->is_featured = false;
        $product->track_inventory = true;
        $product->low_stock_threshold = 5;
        $product->category_id = 1;
        $product->brand_id = 1;

        Log::info('UltraSimpleImporter resolveRecord - PRODUCTO CREADO', [
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku
        ]);

        return $product;
    }

    protected function beforeSave(): void
    {
        Log::info('UltraSimpleImporter beforeSave - INICIO', [
            'record_name' => $this->record->name ?? 'NO_RECORD'
        ]);
    }

    protected function afterSave(): void
    {
        Log::info('UltraSimpleImporter afterSave - PRODUCTO GUARDADO', [
            'record_id' => $this->record->id,
            'record_name' => $this->record->name,
            'was_created' => $this->record->wasRecentlyCreated
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'ULTRA SIMPLE: ' . $import->successful_rows . ' productos importados!';
    }
}
