<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->required(),
                FileUpload::make('mobile_image')
                    ->image(),
                TextInput::make('link'),
                TextInput::make('button_text'),
                TextInput::make('position')
                    ->required()
                    ->default('home_hero'),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('expires_at'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
