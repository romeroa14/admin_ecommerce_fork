<?php

namespace App\Filament\Resources\CartItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class CartItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('cart.user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),

                \Filament\Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->limit(30),

                \Filament\Tables\Columns\TextColumn::make('product.sku')
                    ->label('SKU')
                    ->sortable()
                    ->searchable()
                    ->color('gray')
                    ->copyable(),

                \Filament\Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                \Filament\Tables\Columns\TextColumn::make('price')
                    ->label('Precio Unit.')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd(),

                \Filament\Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),

                \Filament\Tables\Columns\TextColumn::make('cart.total')
                    ->label('Total Carrito')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd()
                    ->color('warning'),

                \Filament\Tables\Columns\TextColumn::make('cart.items_count')
                    ->label('Items en Carrito')
                    ->getStateUsing(fn ($record) => $record->cart->getItemsCount())
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('secondary'),

                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Agregado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('cart.user_id')
                    ->label('Usuario')
                    ->relationship('cart.user', 'name')
                    ->preload(),

                \Filament\Tables\Filters\SelectFilter::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->preload(),

                \Filament\Tables\Filters\Filter::make('high_quantity')
                    ->label('Cantidad Alta')
                    ->query(fn ($query) => $query->where('quantity', '>', 2)),

                \Filament\Tables\Filters\Filter::make('high_value')
                    ->label('Alto Valor')
                    ->query(fn ($query) => $query->where('subtotal', '>', 100)),
            ])
            ->recordActions([
                EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('recalculate_totals')
                        ->label('Recalcular Totales')
                        ->icon('heroicon-o-calculator')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->calculateSubtotal();
                                $record->cart->calculateTotals();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
