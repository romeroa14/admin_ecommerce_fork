<?php

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->weight('bold'),
                ImageColumn::make('image')
                    ->disk('public')
                    ->width(160)
                    ->height(50)
                    ->label('Imagen principal'),
                ImageColumn::make('mobile_image')
                    ->disk('public')
                    ->width(50)
                    ->height(50)
                    ->label('Imagen móvil'),
                TextColumn::make('button_text')
                    ->label('Botón'),
                TextColumn::make('position')
                    ->badge()
                    ->color('info')
                    ->label('Posición'),
                TextColumn::make('order')
                    ->numeric()
                    ->sortable()
                    ->label('Orden'),
                TextColumn::make('starts_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Inicio')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expires_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Vence')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Activo'),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
