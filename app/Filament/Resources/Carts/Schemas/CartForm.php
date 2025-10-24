<?php

namespace App\Filament\Resources\Carts\Schemas;

use App\Models\Product;
use App\Models\User;
use Filament\Schemas\Schema;


class CartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Información del Carrito')
                    ->schema([
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        \Filament\Forms\Components\TextInput::make('session_id')
                            ->label('Session ID')
                            ->disabled()
                            ->dehydrated(),

                        \Filament\Forms\Components\Select::make('coupon_id')
                            ->label('Cupón')
                            ->relationship('coupon', 'code')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        \Filament\Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expira')
                            ->disabled(),
                        
                                \Filament\Forms\Components\Placeholder::make('Resumen del Carrito')
                                    ->label('Resumen del Carrito')
                                    ->content(function ($record) {
                                        if (!$record || !$record->items || empty($record->items)) {
                                            return '<div style="text-align: center; padding: 20px; color: #6b7280; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <p style="margin: 0;">No hay items en el carrito</p>
                                                        <p style="margin: 5px 0 0 0; font-size: 14px;">Los items se agregan desde la página de productos</p>
                                                    </div>';
                                        }

                                        $totals = $record->getTotals();
                                        $currencySymbol = \App\Helpers\CurrencyHelper::getCurrentCurrencySymbol();
                                        
                                        return "
                                            <div style='background: #f9fafb; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb;'>
                                                <h3 style='margin: 0 0 15px 0; color: #374151; font-size: 18px;'></h3>
                                                <div style='margin-bottom: 15px;'>
                                                    <div style='display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>
                                                        <span style='font-weight: 500;'>Subtotal:</span>
                                                        <span style='font-weight: 600;'>{$currencySymbol}{$totals['subtotal']}</span>
                                                    </div>
                                                    <div style='display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>
                                                        <span style='font-weight: 500;'>Descuento:</span>
                                                        <span style='font-weight: 600; color: #f59e0b;'>-{$currencySymbol}{$totals['discount_amount']}</span>
                                                    </div>
                                                    <div style='display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;'>
                                                        <span style='font-weight: 500;'>Impuestos (21%):</span>
                                                        <span style='font-weight: 600;'>{$currencySymbol}{$totals['tax_amount']}</span>
                                                    </div>
                                                </div>
                                                <div style='display: flex; justify-content: space-between; padding: 12px 0; border-top: 2px solid #22c55e; background: #f0fdf4; border-radius: 6px; padding: 12px;'>
                                                    <span style='font-weight: bold; font-size: 18px; color: #059669;'>Total:</span>
                                                    <span style='font-weight: bold; font-size: 20px; color: #059669;'>{$currencySymbol}{$totals['total']}</span>
                                                </div>
                                            </div>
                                        ";
                                    })
                                    ->html()
                                    ->columnSpanFull()
                                    // ->collapsible()
                                    ->columns(1),
                            // ])
                            // ->columns(1)
                            // ->collapsible()
                            // ->columnSpanFull()
                            // ->extraAttributes([
                            //     'class' => 'sticky-totals-section',
                            //     'style' => 'position: sticky; top: 20px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 10;'
                            // ]),

                    ])
                    ->columns(2)
                    ->collapsible(),



                \Filament\Schemas\Components\Section::make('Items del Carrito')
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('items')
                            ->label('Items del Carrito')
                            ->schema([
                                \Filament\Forms\Components\Hidden::make('product_id')
                                    ->dehydrated(),

                                \Filament\Forms\Components\TextInput::make('product_name')
                                    ->label('Producto')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function ($component, $state, $get) {
                                        $productId = $get('product_id');
                                        if ($productId) {
                                            $product = \App\Models\Product::find($productId);
                                            $productName = $product ? $product->name : "Producto ID: {$productId}";
                                            $component->state($productName);
                                        }
                                    }),

                                \Filament\Forms\Components\TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),

                                \Filament\Forms\Components\TextInput::make('price')
                                    ->label('Precio')
                                    ->numeric()
                                    ->prefix('€')
                                    ->disabled()
                                    ->dehydrated(),

                                \Filament\Forms\Components\TextInput::make('discount_percentage')
                                    ->label('Descuento (%)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->disabled()
                                    ->dehydrated(),

                                \Filament\Forms\Components\Hidden::make('variants')
                                    ->dehydrated(),

                                \Filament\Forms\Components\TextInput::make('variants_display')
                                    ->label('Variantes')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function ($component, $state, $get) {
                                        $variants = $get('variants');
                                        if ($variants && is_array($variants)) {
                                            $variantText = [];
                                            foreach ($variants as $group => $variant) {
                                                $variantText[] = "{$group}: {$variant}";
                                            }
                                            $component->state(implode(', ', $variantText));
                                        } else {
                                            $component->state('Sin variantes');
                                        }
                                    }),
                            ])
                            ->defaultItems(0)
                            ->collapsible()
                            ->columns(2),
                    ])
                    ->columns(1)
                    ->collapsible(),


            ]);
    }
}
