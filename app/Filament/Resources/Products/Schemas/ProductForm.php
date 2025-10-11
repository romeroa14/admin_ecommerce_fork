<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Tag;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Básica del Producto')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre del Producto')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, $state) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->rules(['alpha_dash']),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->rules(['alpha_dash']),

                                TextInput::make('price')
                                    ->label('Precio')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01),

                                TextInput::make('compare_price')
                                    ->label('Precio de Comparación')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('cost')
                                    ->label('Precio de Costo')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01),

                                TextInput::make('low_stock_threshold')
                                    ->label('Umbral de Stock Bajo')
                                    ->numeric()
                                    ->default(10)
                                    ->minValue(0),
                            ]),

                        Textarea::make('short_description')
                            ->label('Descripción Corta')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descripción Completa')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Categorización y Organización')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category_id')
                                    ->label('Categoría')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('slug')
                                            ->required()
                                            ->unique(),
                                        Textarea::make('description'),
                                        Toggle::make('is_active')
                                            ->default(true),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return Category::create($data)->getKey();
                                    }),

                                Select::make('brand_id')
                                    ->label('Marca')
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('slug')
                                            ->required()
                                            ->unique(),
                                        Textarea::make('description'),
                                        Toggle::make('is_active')
                                            ->default(true),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return Brand::create($data)->getKey();
                                    }),
                            ]),

                        TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->suggestions(Tag::pluck('name')->toArray())
                            ->placeholder('Agregar etiquetas...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Configuración de Inventario')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('stock')
                                    ->label('Cantidad en Stock')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                Toggle::make('track_inventory')
                                    ->label('Rastrear Inventario')
                                    ->default(true),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'draft' => 'Borrador',
                                        'active' => 'Activo',
                                        'archived' => 'Archivado',
                                    ])
                                    ->default('draft')
                                    ->required(),

                                Toggle::make('is_featured')
                                    ->label('Producto Destacado')
                                    ->default(false),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Imágenes del Producto')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Imágenes del Producto')
                            ->multiple()
                            ->image()
                            ->maxFiles(10)
                            ->directory('products')
                            ->disk('public')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Variantes del Producto')
                    ->schema([
                        Repeater::make('product_variants')
                            ->label('Variantes del Producto')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre de la Variante')
                                            ->required(),

                                        TextInput::make('sku')
                                            ->label('SKU de la Variante')
                                            ->required(),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('price')
                                            ->label('Precio de la Variante')
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01),

                                        TextInput::make('stock')
                                            ->label('Stock de la Variante')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0),
                                    ]),

                                Toggle::make('is_default')
                                    ->label('Variante por Defecto'),

                                KeyValue::make('attributes')
                                    ->label('Atributos (Talla, Color, etc.)')
                                    ->keyLabel('Atributo')
                                    ->valueLabel('Valor')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('SEO y Metadatos')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Título Meta')
                            ->maxLength(60)
                            ->helperText('Recomendado: 50-60 caracteres'),

                        Textarea::make('meta_description')
                            ->label('Descripción Meta')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Recomendado: 150-160 caracteres')
                            ->columnSpanFull(),

                        TagsInput::make('meta_keywords')
                            ->label('Palabras Clave Meta')
                            ->placeholder('Agregar palabras clave...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
