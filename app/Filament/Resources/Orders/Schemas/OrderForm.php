<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\User;
use App\Models\Address;
use App\Models\Coupon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Pedido')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('order_number')
                                    ->label('Número de Pedido')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => \App\Models\Order::generateOrderNumber()),

                                Select::make('user_id')
                                    ->label('Cliente')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('status')
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
                                    ->required()
                                    ->default('pending'),

                                Select::make('payment_status')
                                    ->label('Estado del Pago')
                                    ->options([
                                        'pending' => 'Pendiente',
                                        'paid' => 'Pagado',
                                        'failed' => 'Fallido',
                                        'refunded' => 'Reembolsado',
                                    ])
                                    ->required()
                                    ->default('pending'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Direcciones')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('shipping_address_id')
                                    ->label('Dirección de Envío')
                                    ->relationship('shippingAddress', 'address_line_1')
                                    ->searchable()
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(fn (Address $record): string => 
                                        $record->first_name . ' ' . $record->last_name . ' - ' . $record->address_line_1
                                    ),

                                Select::make('billing_address_id')
                                    ->label('Dirección de Facturación')
                                    ->relationship('billingAddress', 'address_line_1')
                                    ->searchable()
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(fn (Address $record): string => 
                                        $record->first_name . ' ' . $record->last_name . ' - ' . $record->address_line_1
                                    ),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Información de Pago')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('payment_method')
                                    ->label('Método de Pago')
                                    ->options([
                                        'credit_card' => 'Tarjeta de Crédito',
                                        'debit_card' => 'Tarjeta de Débito',
                                        'paypal' => 'PayPal',
                                        'stripe' => 'Stripe',
                                        'bank_transfer' => 'Transferencia Bancaria',
                                        'cash' => 'Efectivo',
                                    ])
                                    ->searchable(),

                                Select::make('coupon_id')
                                    ->label('Cupón de Descuento')
                                    ->relationship('coupon', 'code')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Totales')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01),

                                TextInput::make('discount_amount')
                                    ->label('Descuento')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->default(0),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('tax_amount')
                                    ->label('Impuestos')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->default(0),

                                TextInput::make('shipping_amount')
                                    ->label('Envío')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->default(0),
                            ]),

                        TextInput::make('total_amount')
                            ->label('Total')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->columnSpanFull(),

                        Placeholder::make('total_calculation')
                            ->label('Cálculo del Total')
                            ->content(function (Get $get): string {
                                $subtotal = $get('subtotal') ?? 0;
                                $discount = $get('discount_amount') ?? 0;
                                $tax = $get('tax_amount') ?? 0;
                                $shipping = $get('shipping_amount') ?? 0;
                                $total = $subtotal - $discount + $tax + $shipping;
                                return 'Subtotal: $' . number_format($subtotal, 2) . 
                                       ' - Descuento: $' . number_format($discount, 2) . 
                                       ' + Impuestos: $' . number_format($tax, 2) . 
                                       ' + Envío: $' . number_format($shipping, 2) . 
                                       ' = Total: $' . number_format($total, 2);
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Notas')
                    ->schema([
                        Textarea::make('customer_notes')
                            ->label('Notas del Cliente')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('admin_notes')
                            ->label('Notas Administrativas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Fechas Importantes')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('confirmed_at')
                                    ->label('Fecha de Confirmación'),

                                DateTimePicker::make('shipped_at')
                                    ->label('Fecha de Envío'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('delivered_at')
                                    ->label('Fecha de Entrega'),

                                DateTimePicker::make('cancelled_at')
                                    ->label('Fecha de Cancelación'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
