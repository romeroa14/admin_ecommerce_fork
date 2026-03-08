<?php

namespace App\Filament\Resources\Refunds\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RefundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->label('Pedido')
                    ->relationship('order', 'id')
                    ->required(),
                Select::make('payment_id')
                    ->label('Pago ID')
                    ->relationship('payment', 'id'),
                TextInput::make('refund_number')
                    ->label('Número de Reembolso')
                    ->required(),
                TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric(),
                TextInput::make('type')
                    ->label('Tipo')
                    ->required()
                    ->default('full'),
                TextInput::make('status')
                    ->label('Estado')
                    ->required()
                    ->default('pending'),
                Textarea::make('reason')
                    ->label('Razón')
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->label('Notas Internas')
                    ->columnSpanFull(),
                DateTimePicker::make('processed_at')
                    ->label('Procesada el'),
            ]);
    }
}
