<?php

namespace App\Filament\Resources\Products\Actions;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DirectCsvImportAction extends Action
{
    public static function getDefaultName(): string
    {
        return 'direct_csv_import';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Importar CSV Directo')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('success')
            ->form([
                FileUpload::make('csv_file')
                    ->label('Archivo CSV')
                    ->acceptedFileTypes(['text/csv', 'application/csv'])
                    ->required()
                    ->helperText('Selecciona un archivo CSV con los productos a importar')
            ])
            ->action(function (array $data): void {
                $this->processCsvFile($data['csv_file']);
            });
    }

    private function processCsvFile(string $filePath): void
    {
        try {
            // Obtener la ruta completa del archivo
            $fullPath = storage_path('app/public/' . $filePath);
            
            // Si no existe en public, intentar en la ruta directa
            if (!file_exists($fullPath)) {
                $fullPath = storage_path('app/' . $filePath);
            }
            
            // Si aún no existe, intentar con la ruta absoluta
            if (!file_exists($fullPath)) {
                $fullPath = $filePath;
            }
            
            // Si aún no existe, intentar con la ruta relativa desde storage
            if (!file_exists($fullPath)) {
                $fullPath = storage_path($filePath);
            }
            
            Log::info('DirectCsvImportAction - Iniciando procesamiento', [
                'original_path' => $filePath,
                'full_path' => $fullPath,
                'exists' => file_exists($fullPath),
                'is_readable' => is_readable($fullPath)
            ]);

            if (!file_exists($fullPath)) {
                throw new \Exception("No se pudo encontrar el archivo CSV. Ruta probada: " . $fullPath);
            }
            
            if (!is_readable($fullPath)) {
                throw new \Exception("El archivo CSV existe pero no se puede leer. Ruta: " . $fullPath);
            }

            // Leer el archivo CSV
            $csvData = $this->readCsvFile($fullPath);
            
            if (empty($csvData)) {
                Notification::make()
                    ->title('Error')
                    ->body('El archivo CSV está vacío o no se pudo leer')
                    ->danger()
                    ->send();
                return;
            }

            // Procesar cada fila
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($csvData as $index => $row) {
                try {
                    $this->insertProductDirectly($row);
                    $successCount++;
                    Log::info('DirectCsvImportAction - Producto insertado', [
                        'row' => $index + 1,
                        'name' => $row['name'] ?? 'Sin nombre'
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Fila " . ($index + 1) . ": " . $e->getMessage();
                    Log::error('DirectCsvImportAction - Error en fila', [
                        'row' => $index + 1,
                        'error' => $e->getMessage(),
                        'data' => $row
                    ]);
                }
            }

            // Mostrar resultado
            if ($successCount > 0) {
                Notification::make()
                    ->title('Importación Completada')
                    ->body("✅ {$successCount} productos importados exitosamente")
                    ->success()
                    ->send();
            }

            if ($errorCount > 0) {
                Notification::make()
                    ->title('Errores Encontrados')
                    ->body("❌ {$errorCount} productos no se pudieron importar")
                    ->warning()
                    ->send();
            }

        } catch (\Exception $e) {
            Log::error('DirectCsvImportAction - Error general', [
                'error' => $e->getMessage()
            ]);

            Notification::make()
                ->title('Error en la Importación')
                ->body('Ocurrió un error al procesar el archivo CSV: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function readCsvFile(string $filePath): array
    {
        $csvData = [];
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            throw new \Exception('No se pudo abrir el archivo CSV');
        }

        // Leer la primera fila (headers)
        $headers = fgetcsv($handle);
        
        if (!$headers) {
            fclose($handle);
            throw new \Exception('El archivo CSV no tiene headers válidos');
        }

        // Leer cada fila de datos
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $csvData[] = array_combine($headers, $row);
            }
        }

        fclose($handle);
        return $csvData;
    }

    private function insertProductDirectly(array $row): void
    {
        // Manejar categoría
        $categoryId = 1; // Por defecto
        if (isset($row['category_name']) && !empty($row['category_name'])) {
            $category = Category::firstOrCreate(
                ['name' => $row['category_name']],
                ['slug' => Str::slug($row['category_name'])]
            );
            $categoryId = $category->id;
        }

        // Manejar marca
        $brandId = 1; // Por defecto
        if (isset($row['brand_name']) && !empty($row['brand_name'])) {
            $brand = Brand::firstOrCreate(
                ['name' => $row['brand_name']],
                ['slug' => Str::slug($row['brand_name'])]
            );
            $brandId = $brand->id;
        }

        // Preparar datos para INSERT directo
        $productData = [
            'name' => $row['name'] ?? 'Producto Sin Nombre',
            'slug' => Str::slug($row['name'] ?? 'producto') . '-' . time(),
            'sku' => $row['sku'] ?? 'SKU-' . Str::upper(Str::slug($row['name'] ?? 'producto', '')) . '-' . time(),
            'description' => $row['description'] ?? 'Descripción por defecto',
            'short_description' => $row['short_description'] ?? 'Descripción corta',
            'price' => (float) ($row['price'] ?? 0),
            'compare_price' => isset($row['compare_price']) && !empty($row['compare_price']) ? (float) $row['compare_price'] : null,
            'cost' => isset($row['cost']) && !empty($row['cost']) ? (float) $row['cost'] : 0,
            'discount_percentage' => isset($row['discount_percentage']) && !empty($row['discount_percentage']) ? (float) $row['discount_percentage'] : null,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'stock' => (int) ($row['stock'] ?? 0),
            'low_stock_threshold' => (int) ($row['low_stock_threshold'] ?? 5),
            'track_inventory' => $row['track_inventory'] !== '0' && $row['track_inventory'] !== 'false' && $row['track_inventory'] !== 'no',
            'status' => $row['status'] ?? 'active',
            'is_featured' => $row['is_featured'] === '1' || $row['is_featured'] === 'true' || $row['is_featured'] === 'yes',
            'meta_title' => $row['meta_title'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'meta_keywords' => isset($row['meta_keywords']) && !empty($row['meta_keywords']) ? json_encode(explode(',', $row['meta_keywords'])) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // INSERT directo a la base de datos
        $productId = DB::table('products')->insertGetId($productData);
        
        Log::info('DirectCsvImportAction - INSERT directo exitoso', [
            'product_id' => $productId,
            'name' => $productData['name'],
            'sku' => $productData['sku']
        ]);
    }
}
