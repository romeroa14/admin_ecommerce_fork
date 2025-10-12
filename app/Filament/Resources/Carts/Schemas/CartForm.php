<?php

namespace App\Filament\Resources\Carts\Schemas;

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
                            ->required()
                            ->searchable()
                            ->preload(),

                        \Filament\Forms\Components\TextInput::make('session_id')
                            ->label('ID de Sesión')
                            ->placeholder('Para usuarios no autenticados'),
                    ]),

                \Filament\Schemas\Components\Section::make('Totales')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        \Filament\Forms\Components\TextInput::make('discount_amount')
                            ->label('Descuento')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        \Filament\Forms\Components\TextInput::make('tax_amount')
                            ->label('Impuestos')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        \Filament\Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        \Filament\Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expira')
                            ->disabled(),
                    ]),
            ]);
    }
}
