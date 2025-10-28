<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Payment;

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
                    ->getStateUsing(function ($record) {
                        return current_currency_symbol() . ' ' . $record->amount;
                    })
                    ->sortable()
                    ->alignEnd(),

                

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
                // ViewAction::make(),
                EditAction::make(),
                Action::make('toggle_payment_status')
                    ->label(fn (Payment $record) => $record->status === 'completed' ? 'Marcar como Pendiente' : 'Marcar como Completado')
                    ->icon(fn (Payment $record) => $record->status === 'completed' ? 'heroicon-o-x-mark' : 'heroicon-o-check')
                    ->color(fn (Payment $record) => $record->status === 'completed' ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Payment $record) => $record->status === 'completed' ? 'Marcar como Pendiente' : 'Marcar como Completado')
                    ->modalDescription(fn (Payment $record) => $record->status === 'completed' 
                        ? '¿Estás seguro de que quieres marcar este pago como PENDIENTE?' 
                        : '¿Estás seguro de que quieres marcar este pago como COMPLETADO?')
                    ->action(function (Payment $record) {
                        $wasCompleted = $record->status === 'completed';
                        
                        if ($wasCompleted) {
                            // Marcar como pendiente
                            $record->status = 'pending';
                            $record->payment_date = null;
                            
                            // Actualizar el Order relacionado
                            if ($record->order) {
                                $record->order->is_paid = false;
                                $record->order->paid_at = null;
                                $record->order->status = 'pending';
                                $record->order->confirmed_at = null;
                                $record->order->save();
                            }
                        } else {
                            // Marcar como completado
                            $record->status = 'completed';
                            $record->payment_date = now();
                            
                            // Actualizar el Order relacionado
                            if ($record->order) {
                                $record->order->is_paid = true;
                                $record->order->paid_at = now();
                                if ($record->order->status === 'pending') {
                                    $record->order->status = 'confirmed';
                                    $record->order->confirmed_at = now();
                                }
                                $record->order->save();
                            }
                        }

                        $record->save();

                        // Notificación de éxito
                        \Filament\Notifications\Notification::make()
                            ->title($wasCompleted ? 'Pago marcado como PENDIENTE' : 'Pago marcado como COMPLETADO')
                            ->body("Pago {$record->transaction_id} actualizado correctamente")
                            ->success()
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
