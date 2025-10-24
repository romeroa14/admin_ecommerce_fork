<?php

namespace App\Filament\Resources\Shipments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_number')
                    ->label('Número de Seguimiento')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Número copiado'),

                TextColumn::make('order.order_number')
                    ->label('Pedido')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.orders.view', $record->order_id)),

                TextColumn::make('order.user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('carrier')
                    ->label('Transportista')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'failed',
                    ]),

                TextColumn::make('shipping_cost')
                    ->label('Costo de Envío')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('shipped_at')
                    ->label('Fecha de Envío')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('delivered_at')
                    ->label('Fecha de Entrega')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('estimated_delivery')
                    ->label('Entrega Estimada')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($record) => $record->estimated_delivery < now() ? 'danger' : 'gray'),

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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
