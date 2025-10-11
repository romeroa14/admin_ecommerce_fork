<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                TextInput::make('transaction_id'),
                TextInput::make('payment_method')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('USD'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('payment_details')
                    ->columnSpanFull(),
                TextInput::make('gateway_response'),
                Textarea::make('error_message')
                    ->columnSpanFull(),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
