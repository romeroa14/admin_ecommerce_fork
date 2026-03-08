<?php

namespace App\Filament\Resources\Currencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Moneda')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre de la Moneda')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Ej: Dólar Americano, Euro, Bolívar Venezolano'),

                                TextInput::make('code')
                                    ->label('Código ISO')
                                    ->required()
                                    ->maxLength(3)
                                    ->unique(ignoreRecord: true)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (strtoupper($state) === 'VES') {
                                            $set('name', 'Bolívares (VES)');
                                            $set('symbol', 'Bs');
                                            $rate = \App\Services\ExchangeRateService::getOfficialRate();
                                            if ($rate) {
                                                $set('exchange_rate', $rate);
                                            }
                                        }
                                    })
                                    ->helperText('Código ISO 4217 (USD, EUR, VES)')
                                    ->rules(['regex:/^[A-Z]{3}$/']),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('symbol')
                                    ->label('Símbolo')
                                    ->required()
                                    ->maxLength(5)
                                    ->helperText('Ej: $, €, Bs'),

                                Select::make('symbol_position')
                                    ->label('Posición del Símbolo')
                                    ->options([
                                        'before' => 'Antes del número ($100)',
                                        'after' => 'Después del número (100$)',
                                    ])
                                    ->required()
                                    ->default('before'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('decimal_places')
                                    ->label('Decimales')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(4)
                                    ->default(2)
                                    ->helperText('Número de decimales a mostrar'),

                                TextInput::make('sort_order')
                                    ->label('Orden de Visualización')
                                    ->numeric()
                                    ->default(10)
                                    ->helperText('Orden en que aparecerá en las opciones'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Configuración de Tasa de Cambio')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('exchange_rate')
                                    ->label('Tasa de Cambio')
                                    ->numeric()
                                    ->required()
                                    ->step(0.0001)
                                    ->default(1.0000)
                                    ->helperText(function (\Filament\Forms\Get $get) {
                                        $rate = \App\Services\ExchangeRateService::getOfficialRate() ?? 'actual';
                                        if (strtoupper($get('code')) === 'VES') {
                                            return "La tasa en VES es $rate y se actualiza sola automáticamente en tiempo real con DolarAPI. No necesitas hacer fetch.";
                                        }
                                        return "Equivalencia 1 USD = X (La de VES es manejada por API de $rate)";
                                    })
                                    ->suffixActions([
                                        \Filament\Actions\Action::make('fetchBcv')
                                            ->icon('heroicon-o-banknotes')
                                            ->tooltip('Extraer BCV actual')
                                            ->action(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                                if(strtoupper($get('code')) === 'VES') {
                                                    $rate = \App\Services\ExchangeRateService::getOfficialRate();
                                                    if ($rate) {
                                                        $set('exchange_rate', $rate);
                                                        \Filament\Notifications\Notification::make()->success()->title('BCV Aplicado')->send();
                                                    }
                                                }
                                            }),
                                        \Filament\Actions\Action::make('fetchParalelo')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->tooltip('Extraer Paralelo')
                                            ->action(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                                if(strtoupper($get('code')) === 'VES') {
                                                    $rate = \App\Services\ExchangeRateService::getParallelRate();
                                                    if ($rate) {
                                                        $set('exchange_rate', $rate);
                                                        \Filament\Notifications\Notification::make()->success()->title('Paralelo Aplicado')->send();
                                                    }
                                                }
                                            })
                                    ]),

                                Toggle::make('is_default')
                                    ->label('Moneda por Defecto')
                                    ->helperText('Solo puede haber una moneda por defecto')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            // Si se marca como por defecto, desactivar otras
                                            \App\Models\Currency::where('is_default', true)
                                                ->update(['is_default' => false]);
                                        }
                                    }),
                            ]),

                        Toggle::make('is_active')
                            ->label('Activa')
                            ->default(true)
                            ->helperText('Si está inactiva, no aparecerá en las opciones'),
                    ])
                    ->collapsible(),

                Section::make('Información Adicional')
                    ->schema([
                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Información adicional sobre la moneda'),
                    ])
                    ->collapsible(),
            ]);
    }
}