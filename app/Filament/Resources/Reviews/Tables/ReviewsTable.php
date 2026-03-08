<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_id')
                    ->label('Producto')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user_id')
                    ->label('Usuario')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('order_id')
                    ->label('Pedido')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('Calificación')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->searchable(),
                IconColumn::make('is_verified_purchase')
                    ->label('Verificado')
                    ->boolean(),
                TextColumn::make('helpful_count')
                    ->label('Votos Útil')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unhelpful_count')
                    ->label('Votos No útil')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label('Aprobado el')
                    ->dateTime()
                    ->sortable(),
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
