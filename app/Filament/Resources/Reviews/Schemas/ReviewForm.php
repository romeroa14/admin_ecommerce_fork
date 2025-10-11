<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                \Filament\Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                \Filament\Forms\Components\Select::make('order_id')
                    ->label('Pedido')
                    ->relationship('order', 'order_number')
                    ->searchable()
                    ->preload(),

                \Filament\Forms\Components\Select::make('rating')
                    ->label('Calificación')
                    ->options([
                        1 => '1 Estrella',
                        2 => '2 Estrellas',
                        3 => '3 Estrellas',
                        4 => '4 Estrellas',
                        5 => '5 Estrellas',
                    ])
                    ->required()
                    ->default(5),

                TextInput::make('title')
                    ->label('Título')
                    ->placeholder('Título de la reseña')
                    ->maxLength(255),

                Textarea::make('comment')
                    ->label('Comentario')
                    ->rows(4)
                    ->placeholder('Escribe tu reseña aquí...')
                    ->columnSpanFull(),

                \Filament\Forms\Components\FileUpload::make('images')
                    ->label('Imágenes')
                    ->multiple()
                    ->image()
                    ->maxFiles(5)
                    ->directory('reviews')
                    ->columnSpanFull(),

                \Filament\Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ])
                    ->required()
                    ->default('pending'),

                Toggle::make('is_verified_purchase')
                    ->label('Compra Verificada')
                    ->helperText('Indica si esta reseña proviene de una compra verificada')
                    ->default(false),

                TextInput::make('helpful_count')
                    ->label('Útil')
                    ->numeric()
                    ->default(0)
                    ->disabled(),

                TextInput::make('unhelpful_count')
                    ->label('No Útil')
                    ->numeric()
                    ->default(0)
                    ->disabled(),

                DateTimePicker::make('approved_at')
                    ->label('Fecha de Aprobación')
                    ->disabled(),
            ]);
    }
}
