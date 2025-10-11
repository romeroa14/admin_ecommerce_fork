<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                TextInput::make('invoice_number')
                    ->required(),
                DatePicker::make('invoice_date')
                    ->required(),
                DatePicker::make('due_date'),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                Textarea::make('billing_address')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('pdf_path'),
            ]);
    }
}
