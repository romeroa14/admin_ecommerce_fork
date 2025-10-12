<?php

namespace App\Filament\Resources\CartItems\Schemas;

use Filament\Schemas\Schema;

class CartItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Información del Item')
                    ->schema([
                        \Filament\Forms\Components\Select::make('cart_id')
                            ->label('Carrito')
                            ->relationship('cart', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Carrito #{$record->id} - {$record->user->name}")
                            ->required()
                            ->searchable()
                            ->preload(),

                        \Filament\Forms\Components\Select::make('product_id')
                            ->label('Producto')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $product = \App\Models\Product::find($state);
                                    if ($product) {
                                        $set('price', $product->price);
                                    }
                                }
                            }),

                        \Filament\Forms\Components\TextInput::make('product_variant_id')
                            ->label('Variante del Producto')
                            ->placeholder('Opcional - para productos con variantes'),
                    ]),

                \Filament\Schemas\Components\Section::make('Cantidad y Precios')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $price = $get('price');
                                if ($price && $state) {
                                    $set('subtotal', $price * $state);
                                }
                            }),

                        \Filament\Forms\Components\TextInput::make('price')
                            ->label('Precio Unitario')
                            ->numeric()
                            ->required()
                            ->prefix('€')
                            ->step(0.01)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $quantity = $get('quantity');
                                if ($quantity && $state) {
                                    $set('subtotal', $state * $quantity);
                                }
                            }),

                        \Filament\Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('€')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
