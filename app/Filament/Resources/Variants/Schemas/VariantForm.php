<?php

namespace App\Filament\Resources\Variants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\VariantGroup;

class VariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Variante')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre de la Variante')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Large, Small, Rojo, Azul, Algodón')
                            ->helperText('Nombre genérico de la variante (talla, color, material, etc.)'),

                        Select::make('variant_group_id')
                            ->label('Grupo de Variante')
                            ->options(function () {
                                return VariantGroup::active()
                                    ->ordered()
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->placeholder('Selecciona el grupo de variante')
                            ->helperText('Grupo al que pertenece esta variante (Color, Talla, Material, etc.)')
                            ->searchable()
                            ->preload(),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción opcional de la variante')
                            ->helperText('Descripción detallada de la variante'),

                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                            ])
                            ->default('active')
                            ->required()
                            ->helperText('Estado de la variante en el sistema'),

                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Orden de aparición dentro del grupo'),
                    ])
                    ->collapsible(),
            ]);
    }
}
