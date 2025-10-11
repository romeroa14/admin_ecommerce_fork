<?php

namespace App\Filament\Resources\Addresses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AddressesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'success' => 'shipping',
                        'info' => 'billing',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'shipping' => 'Envío',
                        'billing' => 'Facturación',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->getStateUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                TextColumn::make('company')
                    ->label('Empresa')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('address_line_1')
                    ->label('Dirección')
                    ->searchable()
                    ->limit(50)
                    ->weight('bold'),

                TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('state')
                    ->label('Estado')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('postal_code')
                    ->label('Código Postal')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('country')
                    ->label('País')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('is_default')
                    ->label('Por Defecto')
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
                SelectFilter::make('type')
                    ->label('Tipo de Dirección')
                    ->options([
                        'shipping' => 'Envío',
                        'billing' => 'Facturación',
                    ])
                    ->multiple(),

                SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                \Filament\Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Dirección por Defecto')
                    ->placeholder('Todas las direcciones')
                    ->trueLabel('Solo por defecto')
                    ->falseLabel('No por defecto'),
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
