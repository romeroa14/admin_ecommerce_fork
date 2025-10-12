<?php

namespace App\Filament\Resources\Carts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class CartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                \Filament\Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->getStateUsing(fn ($record) => $record->getItemsCount())
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                \Filament\Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd(),

                \Filament\Tables\Columns\TextColumn::make('discount_amount')
                    ->label('Descuento')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd()
                    ->color('warning'),

                \Filament\Tables\Columns\TextColumn::make('tax_amount')
                    ->label('Impuestos')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd(),

                \Filament\Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),

                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->preload(),

                \Filament\Tables\Filters\Filter::make('has_items')
                    ->label('Con Items')
                    ->query(fn ($query) => $query->whereHas('items')),

                \Filament\Tables\Filters\Filter::make('empty_carts')
                    ->label('Carritos Vacíos')
                    ->query(fn ($query) => $query->whereDoesntHave('items')),

                \Filament\Tables\Filters\Filter::make('expired')
                    ->label('Expirados')
                    ->query(fn ($query) => $query->where('expires_at', '<', now())),
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
