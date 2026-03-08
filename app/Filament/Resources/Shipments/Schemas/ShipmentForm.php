<?php

namespace App\Filament\Resources\Shipments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->label('Pedido')
                    ->relationship('order', 'id')
                    ->required(),
                TextInput::make('tracking_number')
                    ->label('Número de Seguimiento'),
                TextInput::make('carrier')
                    ->label('Transportista'),
                TextInput::make('service_level')
                    ->label('Nivel de Servicio'),
                TextInput::make('status')
                    ->label('Estado')
                    ->required()
                    ->default('pending'),
                TextInput::make('shipping_cost')
                    ->label('Costo de Envío')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('shipping_address')
                    ->label('Dirección de Envío')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
                DateTimePicker::make('shipped_at')
                    ->label('Fecha de Envío'),
                DateTimePicker::make('delivered_at')
                    ->label('Fecha de Entrega'),
                DateTimePicker::make('estimated_delivery')
                    ->label('Entrega Estimada'),
            ]);
    }
}
