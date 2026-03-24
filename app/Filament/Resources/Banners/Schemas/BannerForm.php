<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

/**
 * MEDIDAS RECOMENDADAS PARA BANNERS
 * ─────────────────────────────────
 * Imagen principal (Desktop):
 *   • Dimensiones: 1920 × 600 px
 *   • Relación de aspecto: 16:5 (32:10)
 *   • Formato: JPG o WebP (calidad 85%)
 *   • Peso máximo: 500 KB
 *
 * Imagen móvil:
 *   • Dimensiones: 800 × 800 px
 *   • Relación de aspecto: 1:1 (cuadrada)
 *   • Formato: JPG o WebP
 *   • Peso máximo: 200 KB
 */
class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Título'),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3)
                    ->label('Descripción'),

                // ── Imagen principal 1920×600 px ──────────────────────────
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('banners')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(1024)                   // 1 MB máximo
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:5')    // 1920×600 px
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('600')
                    ->imageEditorAspectRatios(['16:5'])
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Medida recomendada: 1920 × 600 px · Relación 16:5 · JPG/WebP · máx. 1 MB'),

                // ── Imagen móvil 800×800 px ───────────────────────────────
                FileUpload::make('mobile_image')
                    ->image()
                    ->disk('public')
                    ->directory('banners')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(512)
                    ->imageCropAspectRatio('1:1')     // 800×800 px cuadrada
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('800')
                    ->columnSpanFull()
                    ->helperText('Medida recomendada: 800 × 800 px · Relación 1:1 (cuadrada) · JPG/WebP · máx. 512 KB'),

                TextInput::make('button_text')
                    ->placeholder('Ver Más')
                    ->default('Ver Más')
                    ->label('Texto del botón'),

                Select::make('position')
                    ->required()
                    ->default('home_hero')
                    ->label('Posición')
                    ->options([
                        'home_hero'     => 'Hero Principal (Carrusel grande - inicio)',
                        'home_middle'   => 'Banner Central Superior (sobre Artículos Nuevos)',
                        'home_middle_2' => 'Banner Mediano Central (Entre Nuevos y Más Vendidos)',
                        'home_bottom'   => 'Banner pequeño (encima del footer)',
                    ]),

                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->label('Orden (menor = primero)'),

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
