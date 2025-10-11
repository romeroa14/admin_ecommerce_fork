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
                    ->relationship('order', 'id')
                    ->required(),
                Select::make('payment_id')
                    ->relationship('payment', 'id'),
                TextInput::make('refund_number')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('type')
                    ->required()
                    ->default('full'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('reason')
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->columnSpanFull(),
                DateTimePicker::make('processed_at'),
            ]);
    }
}
