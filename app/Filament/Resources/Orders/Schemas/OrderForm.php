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
use Illuminate\Support\Facades\Log;

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
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('cart_id', null);
                                    }),
                            ]),

                        Select::make('cart_id')
                            ->label('Carrito del Cliente')
                            ->options(function (callable $get) {
                                $userId = $get('user_id');
                                if (!$userId) {
                                    return [];
                                }
                                
                                $carts = \App\Models\Cart::where('user_id', $userId)
                                    ->whereNotNull('items')
                                    ->whereRaw("jsonb_array_length(items::jsonb) > 0")
                                    ->get();
                                
                                $options = [];
                                foreach ($carts as $cart) {
                                    $itemsCount = $cart->getItemsCount();
                                    $totals = $cart->getTotals();
                                    $total = $totals['total'];
                                    $currencySymbol = \App\Helpers\CurrencyHelper::getCurrentCurrencySymbol();
                                    $options[$cart->id] = "Carrito #{$cart->id} - {$itemsCount} items - {$currencySymbol}{$total}";
                                }
                                
                                return $options;
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $cart = \App\Models\Cart::find($state);
                                    if ($cart) {
                                        // Obtener totales calculados del carrito
                                        $totals = $cart->getTotals();
                                        
                                        $set('subtotal', $totals['subtotal']);
                                        $set('discount_amount', $totals['discount_amount']);
                                        $set('tax_amount', $totals['tax_amount']);
                                        $set('total_amount', $totals['total']);
                                    }
                                }
                            })
                            ->helperText('Selecciona un carrito activo del cliente para importar los productos y totales'),

                        Select::make('status')
                            ->label('Estado del Pedido')
                            ->options([
                                'pending' => 'Pendiente',
                                'processing' => 'Procesando',
                                'confirmed' => 'Confirmado',
                                'cancelled' => 'Cancelado',
                                'refunded' => 'Reembolsado',
                            ])
                            ->required()
                            ->default('pending')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Si el estado es 'confirmed', marcar para crear factura automáticamente
                                if ($state === 'confirmed') {
                                    $set('auto_create_invoice', true);
                                } else {
                                    $set('auto_create_invoice', false);
                                }
                            }),
                    ])
                    ->collapsible(),

                Section::make('Direcciones')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('shipping_address_id')
                                    ->label('Dirección de Envío')
                                    ->options(function (callable $get) {
                                        $userId = $get('user_id');
                                        if (!$userId) {
                                            return [];
                                        }
                                        
                                        $addresses = Address::where('user_id', $userId)->get();
                                        $options = [];
                                        foreach ($addresses as $address) {
                                            $options[$address->id] = $address->first_name . ' ' . $address->last_name . ' - ' . $address->address_line_1;
                                        }
                                        return $options;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        \Filament\Forms\Components\TextInput::make('first_name')
                                            ->label('Nombre')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('last_name')
                                            ->label('Apellido')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('address_line_1')
                                            ->label('Dirección')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('city')
                                            ->label('Ciudad')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('postal_code')
                                            ->label('Código Postal')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('country')
                                            ->label('País')
                                            ->required()
                                            ->default('España'),
                                    ])
                                    ->createOptionUsing(function (array $data, callable $get) {
                                        $userId = $get('user_id');
                                        if (!$userId) {
                                            throw new \Exception('Debe seleccionar un cliente primero');
                                        }
                                        
                                        $data['user_id'] = $userId;
                                        $data['type'] = 'shipping';
                                        return Address::create($data)->id;
                                    }),

                                Select::make('billing_address_id')
                                    ->label('Dirección de Facturación')
                                    ->options(function (callable $get) {
                                        $userId = $get('user_id');
                                        if (!$userId) {
                                            return [];
                                        }
                                        
                                        $addresses = Address::where('user_id', $userId)->get();
                                        $options = [];
                                        foreach ($addresses as $address) {
                                            $options[$address->id] = $address->first_name . ' ' . $address->last_name . ' - ' . $address->address_line_1;
                                        }
                                        return $options;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        \Filament\Forms\Components\TextInput::make('first_name')
                                            ->label('Nombre')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('last_name')
                                            ->label('Apellido')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('address_line_1')
                                            ->label('Dirección')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('city')
                                            ->label('Ciudad')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('postal_code')
                                            ->label('Código Postal')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('country')
                                            ->label('País')
                                            ->required()
                                            ->default('España'),
                                    ])
                                    ->createOptionUsing(function (array $data, callable $get) {
                                        $userId = $get('user_id');
                                        if (!$userId) {
                                            throw new \Exception('Debe seleccionar un cliente primero');
                                        }
                                        
                                        $data['user_id'] = $userId;
                                        $data['type'] = 'billing';
                                        return Address::create($data)->id;
                                    }),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Información de Pago')
                    ->schema([
                        Select::make('coupon_id')
                            ->label('Cupón de Descuento')
                            ->relationship('coupon', 'code')
                            ->searchable()
                            ->preload(),

                        Grid::make(2)
                            ->schema([
                                Select::make('shipping_id')
                                    ->label('Método de Envío')
                                    ->relationship('shipping', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        Log::info('OrderForm: Método de envío seleccionado', ['shipping_id' => $state]);
                                        
                                        if ($state) {
                                            $shipping = \App\Models\Shipping::find($state);
                                            if ($shipping) {
                                                // Obtener subtotal del carrito o del formulario
                                                $subtotal = $get('subtotal') ?? 0;
                                                
                                                Log::info('OrderForm: Valores antes del cálculo', [
                                                    'shipping_method' => $shipping->name,
                                                    'subtotal' => $subtotal,
                                                    'base_price' => $shipping->base_price
                                                ]);
                                                
                                                // Calcular costo de envío
                                                $shippingCost = $shipping->calculateShippingCost($subtotal);
                                                
                                                Log::info('OrderForm: Costo calculado', ['shipping_cost' => $shippingCost]);
                                                
                                                // Establecer el costo de envío
                                                $set('shipping_amount', $shippingCost);
                                                
                                                // Recalcular total final
                                                self::calculateTotal($set, $get);
                                            }
                                        } else {
                                            // Si no hay método de envío seleccionado, costo = 0
                                            Log::info('OrderForm: No hay método de envío seleccionado, costo = 0');
                                            $set('shipping_amount', 0);
                                            self::calculateTotal($set, $get);
                                        }
                                    })
                                    ->helperText('Selecciona el método de envío para calcular automáticamente el costo'),

                                TextInput::make('shipping_amount')
                                    ->label('Costo de Envío')
                                    ->numeric()
                                    ->prefix(fn() => \App\Helpers\CurrencyHelper::getCurrentCurrencySymbol())
                                    ->step(0.01)
                                    ->live()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        self::calculateTotal($set, $get);
                                    })
                                    ->helperText('Se calcula automáticamente, pero puedes editarlo manualmente'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Totales del Pedido')
                    ->schema([
                        

                        Grid::make(2)
                            ->schema([
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->required()
                                    ->numeric()
                                    ->prefix(fn() => \App\Helpers\CurrencyHelper::getCurrentCurrencySymbol())
                                    ->step(0.01)
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('discount_amount')
                                    ->label('Descuento')
                                    ->numeric()
                                    ->prefix(fn() => \App\Helpers\CurrencyHelper::getCurrentCurrencySymbol())
                                    ->step(0.01)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        self::calculateTotal($set, $get);
                                    }),
                            ]),

                        TextInput::make('tax_amount')
                            ->label('Impuestos')
                            ->numeric()
                            ->prefix(fn() => \App\Helpers\CurrencyHelper::getCurrentCurrencySymbol())
                            ->step(0.01)
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::calculateTotal($set, $get);
                            })
                            ->columnSpanFull(),

                        TextInput::make('total_amount')
                            ->label('Total Final')
                            ->required()
                            ->numeric()
                            ->prefix(fn() => \App\Helpers\CurrencyHelper::getCurrentCurrencySymbol())
                            ->step(0.01)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull(),

                        Placeholder::make('total_calculation')
                            ->label('Cálculo del Total Final')
                            ->content(function (Get $get): string {
                                $subtotal = $get('subtotal') ?? 0;
                                $discount = $get('discount_amount') ?? 0;
                                $tax = $get('tax_amount') ?? 0;
                                $shipping = $get('shipping_amount') ?? 0;
                                $total = $subtotal - $discount + $tax + $shipping;
                                
                                // Obtener información del método de envío
                                $shippingId = $get('shipping_id');
                                $shippingInfo = '';
                                if ($shippingId) {
                                    $shippingMethod = \App\Models\Shipping::find($shippingId);
                                    if ($shippingMethod) {
                                        $shippingInfo = " ({$shippingMethod->name})";
                                    }
                                }
                                
                                // Obtener símbolo de moneda actual
                                $currencySymbol = \App\Helpers\CurrencyHelper::getCurrentCurrencySymbol();
                                
                                return "
                                    <div style='background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;'>
                                        <h4 style='margin: 0 0 10px 0; color: #1e293b; font-size: 16px;'>Desglose del Total</h4>
                                        <div style='display: flex; justify-content: space-between; margin: 5px 0;'>
                                            <span>Subtotal:</span>
                                            <span style='font-weight: 600;'>{$currencySymbol}" . number_format($subtotal, 2) . "</span>
                                        </div>
                                        <div style='display: flex; justify-content: space-between; margin: 5px 0; color: #f59e0b;'>
                                            <span>Descuento:</span>
                                            <span style='font-weight: 600;'>-{$currencySymbol}" . number_format($discount, 2) . "</span>
                                        </div>
                                        <div style='display: flex; justify-content: space-between; margin: 5px 0;'>
                                            <span>Impuestos (21%):</span>
                                            <span style='font-weight: 600;'>{$currencySymbol}" . number_format($tax, 2) . "</span>
                                        </div>
                                        <div style='display: flex; justify-content: space-between; margin: 5px 0; color: #3b82f6;'>
                                            <span>Envío{$shippingInfo}:</span>
                                            <span style='font-weight: 600;'>{$currencySymbol}" . number_format($shipping, 2) . "</span>
                                        </div>
                                        <hr style='margin: 10px 0; border: none; border-top: 2px solid #22c55e;'>
                                        <div style='display: flex; justify-content: space-between; margin: 5px 0; font-size: 18px; font-weight: bold; color: #059669;'>
                                            <span>Total Final:</span>
                                            <span>{$currencySymbol}" . number_format($total, 2) . "</span>
                                        </div>
                                    </div>
                                ";
                            })
                            ->html()
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

                // Campo oculto para controlar la creación automática de factura
                \Filament\Forms\Components\Hidden::make('auto_create_invoice')
                    ->default(false),

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

    private static function calculateTotal(callable $set, callable $get): void
    {
        $subtotal = $get('subtotal') ?? 0;
        $discount = $get('discount_amount') ?? 0;
        $tax = $get('tax_amount') ?? 0;
        $shipping = $get('shipping_amount') ?? 0;
        $total = $subtotal - $discount + $tax + $shipping;
        
        $set('total_amount', $total);
    }
}
