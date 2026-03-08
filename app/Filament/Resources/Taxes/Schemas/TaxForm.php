<?php

namespace App\Filament\Resources\Taxes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TaxForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('country')
                    ->label('País')
                    ->required(),
                TextInput::make('state')
                    ->label('Estado/Provincia'),
                TextInput::make('zip_code')
                    ->label('Código Postal'),
                TextInput::make('rate')
                    ->label('Tasa (%)')
                    ->required()
                    ->numeric(),
                Toggle::make('is_compound')
                    ->label('Es Compuesto')
                    ->required(),
                TextInput::make('priority')
                    ->label('Prioridad')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->required(),
            ]);
    }
}
