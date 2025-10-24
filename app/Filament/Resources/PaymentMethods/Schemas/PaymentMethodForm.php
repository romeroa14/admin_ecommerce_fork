<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Método de Pago')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('Código único para identificar el método (ej: credit_card, paypal)'),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->helperText('Número para ordenar los métodos de pago'),

                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->collapsible(),

                Section::make('Apariencia')
                    ->schema([
                        TextInput::make('icon')
                            ->label('Icono')
                            ->maxLength(100)
                            ->helperText('Nombre del icono de Heroicons (ej: credit-card, banknotes)'),

                        ColorPicker::make('color')
                            ->label('Color')
                            ->default('primary')
                            ->helperText('Color del badge en la interfaz'),
                    ])
                    ->collapsible(),

                Section::make('Configuración del Gateway')
                    ->schema([
                        Toggle::make('requires_gateway')
                            ->label('Requiere Gateway')
                            ->live()
                            ->helperText('Si este método requiere configuración de gateway externo'),

                        KeyValue::make('gateway_config')
                            ->label('Configuración del Gateway')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar Configuración')
                            ->visible(fn (callable $get) => $get('requires_gateway'))
                            ->helperText('Configuraciones específicas para el gateway (API keys, endpoints, etc.)'),
                    ])
                    ->collapsible(),
            ]);
    }
}
