<?php

namespace App\Filament\Resources\Shippings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ShippingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Método de Envío')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre del Método')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Ej: Envío Estándar, Envío Express'),

                                TextInput::make('code')
                                    ->label('Código')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Código único para identificar el método'),
                            ]),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Descripción del método de envío para el cliente'),
                    ])
                    ->collapsible(),

                Section::make('Configuración de Precios')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('base_price')
                                    ->label('Precio Base')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->required()
                                    ->helperText('Precio mínimo de envío'),

                                TextInput::make('price_per_kg')
                                    ->label('Precio por Kg')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->default(0)
                                    ->helperText('Costo adicional por kilogramo'),

                                TextInput::make('free_shipping_threshold')
                                    ->label('Umbral Envío Gratis')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->helperText('Pedidos superiores a este monto tienen envío gratis'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Configuración de Entrega')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('estimated_days_min')
                                    ->label('Días Mínimos')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->helperText('Días mínimos de entrega'),

                                TextInput::make('estimated_days_max')
                                    ->label('Días Máximos')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->helperText('Días máximos de entrega'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Zonas de Envío')
                    ->schema([
                        Repeater::make('zones')
                            ->label('Zonas Disponibles')
                            ->schema([
                                TextInput::make('zone')
                                    ->label('Zona')
                                    ->required()
                                    ->placeholder('Ej: España, Europa, Mundial'),
                            ])
                            ->defaultItems(1)
                            ->collapsible()
                            ->helperText('Zonas donde este método de envío está disponible'),
                    ])
                    ->collapsible(),

                Section::make('Límites de Peso')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('weight_limits.max')
                                    ->label('Peso Máximo (kg)')
                                    ->numeric()
                                    ->step(0.1)
                                    ->helperText('Peso máximo permitido para este método'),

                                TextInput::make('weight_limits.min')
                                    ->label('Peso Mínimo (kg)')
                                    ->numeric()
                                    ->step(0.1)
                                    ->default(0)
                                    ->helperText('Peso mínimo para aplicar este método'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Configuración del Sistema')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Si está inactivo, no aparecerá en las opciones'),

                                TextInput::make('sort_order')
                                    ->label('Orden de Visualización')
                                    ->numeric()
                                    ->default(10)
                                    ->helperText('Orden en que aparecerá en las opciones'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}