<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Imagen')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('/images/placeholder-product.png'))
                    ->getStateUsing(function ($record) {
                        // Si hay imágenes, tomar la primera
                        if ($record->images && is_array($record->images) && count($record->images) > 0) {
                            $imagePath = $record->images[0];
                            // Construir la URL completa
                            return asset('storage/' . $imagePath);
                        }
                        return null;
                    }),

                TextColumn::make('name')
                    ->label('Nombre del Producto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('SKU copiado')
                    ->copyMessageDuration(1500),

                TextColumn::make('price')
                    ->label('Precio')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('compare_price')
                    ->label('Precio de Comparación')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state, Product $record): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= $record->low_stock_threshold => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('brand.name')
                    ->label('Marca')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'draft',
                        'danger' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'draft' => 'Borrador',
                        'archived' => 'Archivado',
                        default => $state,
                    })
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('track_inventory')
                    ->label('Rastrear Inventario')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'draft' => 'Borrador',
                        'archived' => 'Archivado',
                    ])
                    ->multiple(),

                SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('brand_id')
                    ->label('Marca')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_featured')
                    ->label('Productos Destacados')
                    ->placeholder('Todos los productos')
                    ->trueLabel('Solo destacados')
                    ->falseLabel('No destacados'),

                TernaryFilter::make('track_inventory')
                    ->label('Rastrear Inventario')
                    ->placeholder('Todos los productos')
                    ->trueLabel('Rastreo habilitado')
                    ->falseLabel('Rastreo deshabilitado'),

                Filter::make('stock_status')
                    ->label('Estado del Stock')
                    ->form([
                        Select::make('stock_filter')
                            ->label('Filtrar por Stock')
                            ->options([
                                'in_stock' => 'En Stock',
                                'low_stock' => 'Stock Bajo',
                                'out_of_stock' => 'Sin Stock',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['stock_filter'] ?? null) {
                            'in_stock' => $query->where('stock', '>', 0),
                            'low_stock' => $query->whereColumn('stock', '<=', 'low_stock_threshold'),
                            'out_of_stock' => $query->where('stock', '<=', 0),
                            default => $query,
                        };
                    }),

                Filter::make('price_range')
                    ->label('Rango de Precios')
                    ->form([
                        TextInput::make('min_price')
                            ->label('Precio Mínimo')
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('max_price')
                            ->label('Precio Máximo')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['max_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
