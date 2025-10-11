<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Clave')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Ej: site_name'),

                Textarea::make('value')
                    ->label('Valor')
                    ->columnSpanFull()
                    ->rows(3)
                    ->placeholder('Valor de la configuración'),

                TextInput::make('type')
                    ->label('Tipo')
                    ->required()
                    ->default('string')
                    ->placeholder('string, number, boolean, json'),

                TextInput::make('group')
                    ->label('Grupo')
                    ->placeholder('Ej: general, email, payment'),

                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull()
                    ->rows(2)
                    ->placeholder('Descripción de la configuración'),

                Toggle::make('is_public')
                    ->label('Público')
                    ->helperText('Si está habilitado, esta configuración será visible en la API pública')
                    ->default(false),
            ]);
    }
}
