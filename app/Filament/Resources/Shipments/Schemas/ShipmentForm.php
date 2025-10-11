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
                    ->relationship('order', 'id')
                    ->required(),
                TextInput::make('tracking_number'),
                TextInput::make('carrier'),
                TextInput::make('service_level'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('shipping_cost')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('shipping_address')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DateTimePicker::make('shipped_at'),
                DateTimePicker::make('delivered_at'),
                DateTimePicker::make('estimated_delivery'),
            ]);
    }
}
