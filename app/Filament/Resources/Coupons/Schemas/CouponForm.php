<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Cupón')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('code')
                                    ->label('Código')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Ej: WELCOME10'),

                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->placeholder('Ej: Descuento de Bienvenida'),
                            ]),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Tipo de Descuento')
                                    ->options([
                                        'percentage' => 'Porcentaje',
                                        'fixed_cart' => 'Descuento Fijo (Carrito)',
                                        'fixed_product' => 'Descuento Fijo (Producto)',
                                    ])
                                    ->required()
                                    ->default('percentage'),

                                TextInput::make('amount')
                                    ->label('Valor del Descuento')
                                    ->required()
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('$')
                                    ->suffix(fn ($get) => $get('type') === 'percentage' ? '%' : ''),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Configuración Avanzada')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('min_purchase_amount')
                                    ->label('Compra Mínima')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('$'),

                                TextInput::make('max_discount_amount')
                                    ->label('Descuento Máximo')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('$'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('usage_limit')
                                    ->label('Límite de Uso Total')
                                    ->numeric()
                                    ->placeholder('Sin límite'),

                                TextInput::make('usage_limit_per_user')
                                    ->label('Límite por Usuario')
                                    ->numeric()
                                    ->placeholder('Sin límite'),
                            ]),

                        Toggle::make('free_shipping')
                            ->label('Envío Gratis')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->collapsible(),

                Section::make('Restricciones de Productos')
                    ->schema([
                        TextInput::make('product_ids')
                            ->label('IDs de Productos Específicos')
                            ->placeholder('Separados por comas: 1,2,3'),

                        TextInput::make('category_ids')
                            ->label('IDs de Categorías Específicas')
                            ->placeholder('Separados por comas: 1,2,3'),

                        TextInput::make('excluded_product_ids')
                            ->label('Productos Excluidos')
                            ->placeholder('Separados por comas: 1,2,3'),

                        TextInput::make('excluded_category_ids')
                            ->label('Categorías Excluidas')
                            ->placeholder('Separados por comas: 1,2,3'),
                    ])
                    ->collapsible(),

                Section::make('Fechas de Validez')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('starts_at')
                                    ->label('Fecha de Inicio')
                                    ->placeholder('Opcional'),

                                DateTimePicker::make('expires_at')
                                    ->label('Fecha de Expiración')
                                    ->placeholder('Opcional'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
