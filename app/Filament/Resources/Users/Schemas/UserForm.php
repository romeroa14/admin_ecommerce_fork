<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required(),
                Select::make('type')
                    ->label('Tipo de Usuario')
                    ->options([
                        'admin' => 'Administrador',
                        'staff' => 'Staff',
                        'delivery' => 'Repartidor',
                        'customer' => 'Cliente',
                    ])
                    ->required()
                    ->default('customer'),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->dehydrated(fn($state) => filled($state)),
            ]);
    }
}
