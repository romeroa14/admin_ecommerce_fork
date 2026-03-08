<?php

namespace App\Filament\Resources\Taxes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaxesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('country')
                    ->label('País')
                    ->searchable(),
                TextColumn::make('state')
                    ->label('Estado/Provincia')
                    ->searchable(),
                TextColumn::make('zip_code')
                    ->label('Código Postal')
                    ->searchable(),
                TextColumn::make('rate')
                    ->label('Tasa (%)')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_compound')
                    ->label('Compuesto')
                    ->boolean(),
                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creado El')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado El')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
