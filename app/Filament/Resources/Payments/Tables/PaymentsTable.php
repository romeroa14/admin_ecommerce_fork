<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')
                    ->label('ID Transacción')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('order.order_number')
                    ->label('Pedido')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.orders.view', $record->order_id)),

                TextColumn::make('order.user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('paymentMethod.name')
                    ->label('Método de Pago')
                    ->badge()
                    ->color(fn ($record) => $record->paymentMethod?->color ?? 'gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('currency')
                    ->label('Moneda')
                    ->badge()
                    ->color('info'),

                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'gray' => 'cancelled',
                        'secondary' => 'refunded',
                    ]),

                TextColumn::make('payment_date')
                    ->label('Fecha de Pago')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'processing' => 'Procesando',
                        'completed' => 'Completado',
                        'failed' => 'Fallido',
                        'cancelled' => 'Cancelado',
                        'refunded' => 'Reembolsado',
                    ]),

                SelectFilter::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'credit_card' => 'Tarjeta de Crédito',
                        'debit_card' => 'Tarjeta de Débito',
                        'paypal' => 'PayPal',
                        'stripe' => 'Stripe',
                        'bank_transfer' => 'Transferencia Bancaria',
                        'cash' => 'Efectivo',
                        'crypto' => 'Criptomoneda',
                    ]),
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
            ->defaultSort('created_at', 'desc');
    }
}
