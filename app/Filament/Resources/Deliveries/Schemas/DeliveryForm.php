<?php

namespace App\Filament\Resources\Deliveries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class DeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Delivery')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('zone')
                                    ->label('Zona')
                                    ->required(),
                                TextInput::make('destination')
                                    ->label('Destino')
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Monto')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),
                                Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'pending' => 'Pendiente',
                                        'assigned' => 'Asignado',
                                        'shipped' => 'Enviado',
                                        'delivered' => 'Entregado',
                                        'cancelled' => 'Cancelado',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ]),
                    ]),
                Section::make('Asignación')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('delivery_man_id')
                                    ->label('Repartidor')
                                    ->relationship('deliveryMan', 'name', fn($query) => $query->where('type', 'delivery'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('person_id')
                                    ->label('Cliente')
                                    ->relationship('person', 'first_name')
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ]),
            ]);
    }
}
