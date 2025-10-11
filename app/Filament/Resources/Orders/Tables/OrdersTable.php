<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Número de Pedido')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Número copiado'),

                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'confirmed',
                        'primary' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                        'secondary' => 'refunded',
                    ])
                    ->sortable(),

                BadgeColumn::make('payment_status')
                    ->label('Estado de Pago')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'secondary' => 'refunded',
                    ])
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Método de Pago')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'credit_card' => 'Tarjeta de Crédito',
                        'debit_card' => 'Tarjeta de Débito',
                        'paypal' => 'PayPal',
                        'stripe' => 'Stripe',
                        'bank_transfer' => 'Transferencia',
                        'cash' => 'Efectivo',
                        default => $state,
                    })
                    ->toggleable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('confirmed_at')
                    ->label('Confirmado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('shipped_at')
                    ->label('Enviado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('delivered_at')
                    ->label('Entregado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_paid')
                    ->label('Pagado')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_delivered')
                    ->label('Entregado')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado del Pedido')
                    ->options([
                        'pending' => 'Pendiente',
                        'processing' => 'Procesando',
                        'confirmed' => 'Confirmado',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregado',
                        'cancelled' => 'Cancelado',
                        'refunded' => 'Reembolsado',
                    ])
                    ->multiple(),

                SelectFilter::make('payment_status')
                    ->label('Estado de Pago')
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'failed' => 'Fallido',
                        'refunded' => 'Reembolsado',
                    ])
                    ->multiple(),

                SelectFilter::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'credit_card' => 'Tarjeta de Crédito',
                        'debit_card' => 'Tarjeta de Débito',
                        'paypal' => 'PayPal',
                        'stripe' => 'Stripe',
                        'bank_transfer' => 'Transferencia Bancaria',
                        'cash' => 'Efectivo',
                    ])
                    ->multiple(),

                Filter::make('date_range')
                    ->label('Rango de Fechas')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Desde'),
                        DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Filter::make('amount_range')
                    ->label('Rango de Montos')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('min_amount')
                            ->label('Monto Mínimo')
                            ->numeric()
                            ->prefix('$'),
                        \Filament\Forms\Components\TextInput::make('max_amount')
                            ->label('Monto Máximo')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_as_confirmed')
                        ->label('Marcar como Confirmado')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each->confirm();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('mark_as_shipped')
                        ->label('Marcar como Enviado')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->action(function (Collection $records) {
                            $records->each->markAsShipped();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('mark_as_delivered')
                        ->label('Marcar como Entregado')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each->markAsDelivered();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('cancel_orders')
                        ->label('Cancelar Pedidos')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $records->each->cancel('Cancelado en lote');
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
