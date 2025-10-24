<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Factura')
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
                                        $set('subtotal', $order->subtotal);
                                        $set('tax_amount', $order->tax_amount);
                                        $set('discount_amount', $order->discount_amount);
                                        $set('total_amount', $order->total_amount);
                                        $set('billing_address', $order->billingAddress?->getFullAddress());
                                    }
                                }
                            }),

                        TextInput::make('invoice_number')
                            ->label('Número de Factura')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => \App\Models\Invoice::generateInvoiceNumber()),

                        DatePicker::make('invoice_date')
                            ->label('Fecha de Factura')
                            ->required()
                            ->default(now()),

                        DatePicker::make('due_date')
                            ->label('Fecha de Vencimiento')
                            ->default(now()->addDays(30)),
                    ])
                    ->collapsible(),

                Section::make('Totales de la Factura')
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('tax_amount')
                            ->label('Impuestos')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('discount_amount')
                            ->label('Descuento')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('total_amount')
                            ->label('Total')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->collapsible(),

                Section::make('Estado y Dirección')
                    ->schema([
                        Select::make('status')
                            ->label('Estado de la Factura')
                            ->options([
                                'draft' => 'Borrador',
                                'sent' => 'Enviada',
                                'paid' => 'Pagada',
                                'overdue' => 'Vencida',
                                'cancelled' => 'Cancelada',
                            ])
                            ->required()
                            ->default('draft'),

                        Textarea::make('billing_address')
                            ->label('Dirección de Facturación')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                TextInput::make('pdf_path')
                    ->label('Ruta del PDF')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
