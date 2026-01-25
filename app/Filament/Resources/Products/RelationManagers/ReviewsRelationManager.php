<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Review;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';
    protected static ?string $recordTitleAttribute = 'title';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->label('Usuario Registrado')
                    ->helperText('Dejar vacío si es reseña de invitado'),

                TextInput::make('reviewer_name')
                    ->label('Nombre del Revisor')
                    ->maxLength(255)
                    ->visible(fn($get) => !$get('user_id')),

                TextInput::make('reviewer_email')
                    ->label('Email del Revisor')
                    ->email()
                    ->maxLength(255)
                    ->visible(fn($get) => !$get('user_id')),

                Select::make('rating')
                    ->label('Calificación')
                    ->options([
                        1 => '⭐ 1 Estrella',
                        2 => '⭐⭐ 2 Estrellas',
                        3 => '⭐⭐⭐ 3 Estrellas',
                        4 => '⭐⭐⭐⭐ 4 Estrellas',
                        5 => '⭐⭐⭐⭐⭐ 5 Estrellas',
                    ])
                    ->required(),

                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),

                Textarea::make('comment')
                    ->label('Comentario')
                    ->rows(4)
                    ->maxLength(5000),

                FileUpload::make('image')
                    ->label('Imagen')
                    ->image()
                    ->directory('reviews'),

                TextInput::make('youtube_url')
                    ->label('URL de YouTube')
                    ->url()
                    ->helperText('URL del video de YouTube (opcional)'),

                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                    ])
                    ->required()
                    ->default('pending'),

                Toggle::make('is_verified_purchase')
                    ->label('Compra Verificada')
                    ->helperText('Marcar si el usuario compró el producto'),

                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->searchable()
                    ->label('Orden Relacionada')
                    ->helperText('Vincular con una orden específica'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('reviewer_display_name')
                    ->label('Revisor')
                    ->searchable(['reviewer_name', 'reviewer_email'])
                    ->sortable(),

                TextColumn::make('rating')
                    ->label('Calificación')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state))
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Título')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('comment')
                    ->label('Comentario')
                    ->limit(100)
                    ->toggleable(),

                ImageColumn::make('image')
                    ->label('Imagen')
                    ->square()
                    ->toggleable(),

                IconColumn::make('youtube_url')
                    ->label('Video')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                        default => $state,
                    })
                    ->sortable(),

                IconColumn::make('is_verified_purchase')
                    ->label('Verificada')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('helpful_count')
                    ->label('👍 Útil')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                    ]),

                \Filament\Tables\Filters\SelectFilter::make('rating')
                    ->label('Calificación')
                    ->options([
                        5 => '5 Estrellas',
                        4 => '4 Estrellas',
                        3 => '3 Estrellas',
                        2 => '2 Estrellas',
                        1 => '1 Estrella',
                    ]),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
