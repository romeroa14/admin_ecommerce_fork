<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('banners')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:5')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('600')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('mobile_image')
                    ->image()
                    ->disk('public')
                    ->directory('banners')
                    ->imageCropAspectRatio('1:1')
                    ->columnSpanFull(),
                // TextInput::make('link')
                //     ->url()
                //     ->placeholder('https://...'),
                TextInput::make('button_text')
                    ->placeholder('Ver Más')
                    ->default('Ver Más'),
                Select::make('position')
                    ->required()
                    ->default('home_hero')
                    ->options([
                        'home_hero'   => 'Hero Principal (Home)',
                        'home_middle' => 'Medio (Home)',
                        'home_bottom' => 'Inferior (Home)',
                        'sidebar'     => 'Barra lateral',
                        'category'    => 'Página de Categoría',
                        'product'     => 'Página de Producto',
                    ]),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->label('Orden'),
                DateTimePicker::make('starts_at')
                    ->label('Fecha de inicio'),
                DateTimePicker::make('expires_at')
                    ->label('Fecha de expiración'),
                Toggle::make('is_active')
                    ->required()
                    ->default(true)
                    ->label('Activo'),
            ]);
    }
}
