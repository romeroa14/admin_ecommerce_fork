<?php

namespace App\Filament\Resources\Shippings\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class ShippingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('base_price')
                    ->label('Precio Base')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('price_per_kg')
                    ->label('Precio/Kg')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('free_shipping_threshold')
                    ->label('Envío Gratis')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd()
                    ->placeholder('No aplica'),

                TextColumn::make('estimated_days')
                    ->label('Tiempo de Entrega')
                    ->getStateUsing(function ($record) {
                        if ($record->estimated_days_min === $record->estimated_days_max) {
                            return $record->estimated_days_min . ' días';
                        }
                        return $record->estimated_days_min . '-' . $record->estimated_days_max . ' días';
                    })
                    ->sortable(),

                TextColumn::make('zones')
                    ->label('Zonas')
                    ->getStateUsing(function ($record) {
                        if (!$record->zones || empty($record->zones)) {
                            return 'Sin restricciones';
                        }
                        
                        // Asegurar que zones es un array y convertir a string
                        $zones = is_array($record->zones) ? $record->zones : [];
                        $zones = array_filter($zones, function($zone) {
                            return is_string($zone) || is_numeric($zone);
                        });
                        
                        return empty($zones) ? 'Sin restricciones' : implode(', ', $zones);
                    })
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    }),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('orders_count')
                    ->label('Pedidos')
                    ->counts('orders')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('sort_order')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}