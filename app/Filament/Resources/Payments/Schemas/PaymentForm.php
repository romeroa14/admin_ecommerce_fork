<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Pago')
                    ->schema([
                        Select::make('order_id')
                            ->label('Pedido Asociado')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $order = \App\Models\Order::find($state);
                                    if ($order) {
                                        // Auto-llenar datos del pedido
                                        $set('amount', $order->total_amount);
                                        $set('currency', $order->user->currency ?? 'EUR');
                                    }
                                }
                            }),

                        TextInput::make('transaction_id')
                            ->label('ID de Transacción')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => \App\Models\Payment::generateTransactionId()),

                        Select::make('payment_method_id')
                            ->label('Método de Pago')
                            ->relationship('paymentMethod', 'name')
                            ->options(\App\Models\PaymentMethod::active()->ordered()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('amount')
                            ->label('Monto')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),

                        TextInput::make('currency')
                            ->label('Moneda')
                            ->required()
                            ->default('EUR')
                            ->maxLength(3),
                    ])
                    ->collapsible(),

                Section::make('Estado del Pago')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'processing' => 'Procesando',
                                'completed' => 'Completado',
                                'failed' => 'Fallido',
                                'cancelled' => 'Cancelado',
                                'refunded' => 'Reembolsado',
                            ])
                            ->required()
                            ->default('pending')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Si el pago se completa, marcar para crear factura
                                if ($state === 'completed') {
                                    $set('auto_create_invoice', true);
                                } else {
                                    $set('auto_create_invoice', false);
                                }
                            }),

                        DatePicker::make('payment_date')
                            ->label('Fecha de Pago')
                            ->default(now()),

                        TextInput::make('gateway_response')
                            ->label('Respuesta del Gateway')
                            ->maxLength(500),

                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // Campo oculto para controlar la creación automática de factura
                \Filament\Forms\Components\Hidden::make('auto_create_invoice')
                    ->default(false),

                Section::make('Información de Reembolso')
                    ->schema([
                        TextInput::make('refund_amount')
                            ->label('Monto de Reembolso')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),

                        DatePicker::make('refund_date')
                            ->label('Fecha de Reembolso'),

                        TextInput::make('refund_reason')
                            ->label('Motivo del Reembolso')
                            ->maxLength(255),
                    ])
                    ->collapsible()
                    ->visible(fn (callable $get) => in_array($get('status'), ['refunded'])),
            ]);
    }
}
