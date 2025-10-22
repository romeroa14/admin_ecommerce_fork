<?php

namespace App\Filament\Resources\Currencies\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class CurrenciesTable
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

                TextColumn::make('symbol')
                    ->label('Símbolo')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('symbol_position')
                    ->label('Posición')
                    ->formatStateUsing(fn (string $state): string => 
                        $state === 'before' ? 'Antes' : 'Después'
                    )
                    ->sortable(),

                TextColumn::make('decimal_places')
                    ->label('Decimales')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('exchange_rate')
                    ->label('Tasa de Cambio')
                    ->formatStateUsing(fn ($state) => '1 ' . $state . ' USD')
                    ->sortable()
                    ->alignEnd(),

                IconColumn::make('is_default')
                    ->label('Por Defecto')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable()
                    ->alignCenter(),

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