<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

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

            ImportColumn::make('sku')
                ->label('SKU')
                ->rules(['nullable', 'string', 'max:100', 'unique:products,sku']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // Log para debuggear
        Log::info('WorkingProductImporter resolveRecord', [
            'data' => $this->data,
            'creating_new_product' => true
        ]);

        // Siempre crear productos nuevos
        return new Product();
    }

    protected function beforeSave(): void
    {
        // Log para debuggear
        Log::info('WorkingProductImporter beforeSave', [
            'data' => $this->data,
            'record_exists' => $this->record ? 'yes' : 'no',
            'record_data' => $this->record ? $this->record->toArray() : null
        ]);

        // Generar slug automáticamente
        $this->record->slug = Str::slug($this->record->name);

        // Establecer valores por defecto
        $this->record->status = 'active';
        $this->record->is_featured = false;
        $this->record->stock = 0;
    }

    protected function afterSave(): void
    {
        Log::info('WorkingProductImporter afterSave', [
            'record_id' => $this->record->id,
            'record_name' => $this->record->name
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Tu importación de productos se ha completado. ';
        $body .= number_format($import->successful_rows) . ' productos importados exitosamente.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' productos fallaron.';
        }

        return $body;
    }
}
