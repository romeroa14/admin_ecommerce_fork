<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';
    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Revisor')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->label('Usuario Registrado')
                            ->helperText('Dejar vacío si es reseña de invitado'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('reviewer_name')
                                    ->label('Nombre del Revisor')
                                    ->maxLength(255)
                                    ->visible(fn($get) => !$get('user_id')),

                                Forms\Components\TextInput::make('reviewer_email')
                                    ->label('Email del Revisor')
                                    ->email()
                                    ->maxLength(255)
                                    ->visible(fn($get) => !$get('user_id')),
                            ]),
                    ]),

                Forms\Components\Section::make('Reseña')
                    ->schema([
                        Forms\Components\Select::make('rating')
                            ->label('Calificación')
                            ->options([
                                1 => '⭐ 1 Estrella',
                                2 => '⭐⭐ 2 Estrellas',
                                3 => '⭐⭐⭐ 3 Estrellas',
                                4 => '⭐⭐⭐⭐ 4 Estrellas',
                                5 => '⭐⭐⭐⭐⭐ 5 Estrellas',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('comment')
                            ->label('Comentario')
                            ->rows(4)
                            ->maxLength(5000),

                        Forms\Components\FileUpload::make('image')
                            ->label('Imagen')
                            ->image()
                            ->directory('reviews')
                            ->maxSize(5120),

                        Forms\Components\TextInput::make('youtube_url')
                            ->label('URL de YouTube')
                            ->url()
                            ->helperText('URL del video de YouTube (opcional)'),
                    ]),

                Forms\Components\Section::make('Estado y Verificación')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'approved' => 'Aprobada',
                                'rejected' => 'Rechazada',
                            ])
                            ->required()
                            ->native(false)
                            ->default('pending'),

                        Forms\Components\Toggle::make('is_verified_purchase')
                            ->label('Compra Verificada')
                            ->helperText('Marcar si el usuario compró el producto'),

                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'id')
                            ->searchable()
                            ->label('Orden Relacionada')
                            ->helperText('Vincular con una orden específica'),
                    ])
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewer_display_name')
                    ->label('Revisor')
                    ->searchable(['reviewer_name', 'reviewer_email'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Calificación')
                    ->badge()
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state))
                    ->color(fn($state) => match (true) {
                        $state >= 4 => 'success',
                        $state == 3 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('comment')
                    ->label('Comentario')
                    ->limit(100)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen')
                    ->square()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('youtube_url')
                    ->label('Video')
                    ->boolean()
                    ->trueIcon('heroicon-o-video-camera')
                    ->falseIcon('heroicon-o-video-camera-slash')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_verified_purchase')
                    ->label('Verificada')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('helpful_count')
                    ->label('👍 Útil')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                    ]),

                Tables\Filters\SelectFilter::make('rating')
                    ->label('Calificación')
                    ->options([
                        5 => '5 Estrellas',
                        4 => '4 Estrellas',
                        3 => '3 Estrellas',
                        2 => '2 Estrellas',
                        1 => '1 Estrella',
                    ]),

                Tables\Filters\TernaryFilter::make('is_verified_purchase')
                    ->label('Compra Verificada'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(Review $record) => $record->approve())
                    ->visible(fn(Review $record) => $record->status !== 'approved'),

                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(Review $record) => $record->reject())
                    ->visible(fn(Review $record) => $record->status !== 'rejected'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Aprobar Seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->approve()),

                    Tables\Actions\BulkAction::make('reject')
                        ->label('Rechazar Seleccionadas')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->reject()),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
