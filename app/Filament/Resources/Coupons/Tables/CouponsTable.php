<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Código copiado'),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'success',
                        'fixed_cart' => 'info',
                        'fixed_product' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Porcentaje',
                        'fixed_cart' => 'Fijo (Carrito)',
                        'fixed_product' => 'Fijo (Producto)',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('min_purchase_amount')
                    ->label('Compra Mínima')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('max_discount_amount')
                    ->label('Descuento Máximo')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('free_shipping')
                    ->label('Envío Gratis')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('usage_limit')
                    ->label('Límite Total')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => $state ?: 'Sin límite'),

                TextColumn::make('usage_limit_per_user')
                    ->label('Límite por Usuario')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => $state ?: 'Sin límite'),

                TextColumn::make('usage_count')
                    ->label('Usos')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('expires_at')
                    ->label('Expiración')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

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
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'percentage' => 'Porcentaje',
                        'fixed_cart' => 'Fijo (Carrito)',
                        'fixed_product' => 'Fijo (Producto)',
                    ])
                    ->multiple(),

                \Filament\Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos los cupones')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),

                \Filament\Tables\Filters\TernaryFilter::make('free_shipping')
                    ->label('Envío Gratis')
                    ->placeholder('Todos los cupones')
                    ->trueLabel('Con envío gratis')
                    ->falseLabel('Sin envío gratis'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
