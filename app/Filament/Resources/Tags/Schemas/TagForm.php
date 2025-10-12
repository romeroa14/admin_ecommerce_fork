<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre de la Etiqueta')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: Verano, Oferta, Nuevo'),
                
                TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: verano, oferta, nuevo')
                    ->helperText('Versión URL-friendly del nombre'),
                
                Select::make('type')
                    ->label('Tipo de Etiqueta')
                    ->options([
                        'general' => 'General',
                        'promotion' => 'Promoción',
                        'season' => 'Temporada',
                        'new' => 'Nuevo',
                        'featured' => 'Destacado',
                    ])
                    ->required()
                    ->default('general')
                    ->helperText('Categoría de la etiqueta'),
                
                TextInput::make('color')
                    ->label('Color (Hex)')
                    ->placeholder('#FF5733')
                    ->helperText('Color para mostrar en el frontend'),
                
                Toggle::make('is_active')
                    ->label('Activa')
                    ->default(true)
                    ->helperText('Si la etiqueta está visible'),
            ]);
    }
}
