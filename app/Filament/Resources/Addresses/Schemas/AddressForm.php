<?php

namespace App\Filament\Resources\Addresses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Usuario')
                    ->schema([
                        Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('type')
                            ->label('Tipo de Dirección')
                            ->options([
                                'shipping' => 'Envío',
                                'billing' => 'Facturación',
                            ])
                            ->required()
                            ->default('shipping'),
                    ])
                    ->collapsible(),

                Section::make('Información Personal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('Nombre')
                                    ->required(),

                                TextInput::make('last_name')
                                    ->label('Apellido')
                                    ->required(),
                            ]),

                        TextInput::make('company')
                            ->label('Empresa')
                            ->columnSpanFull(),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Dirección')
                    ->schema([
                        TextInput::make('address_line_1')
                            ->label('Dirección Línea 1')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('address_line_2')
                            ->label('Dirección Línea 2')
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('city')
                                    ->label('Ciudad')
                                    ->required(),

                                TextInput::make('state')
                                    ->label('Estado/Provincia'),

                                TextInput::make('postal_code')
                                    ->label('Código Postal')
                                    ->required(),
                            ]),

                        TextInput::make('country')
                            ->label('País')
                            ->required()
                            ->default('US')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Configuración')
                    ->schema([
                        Toggle::make('is_default')
                            ->label('Dirección por Defecto')
                            ->helperText('Marcar como dirección principal del usuario')
                            ->default(false),
                    ])
                    ->collapsible(),
            ]);
    }
}
