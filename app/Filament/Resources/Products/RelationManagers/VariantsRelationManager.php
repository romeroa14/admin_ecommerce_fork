<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use App\Models\Variant;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                

                Select::make('variant_id')
                    ->label('Seleccionar Variante')
                    ->options(function () {
                        return Variant::active()
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Selecciona una variante existente para asociar al producto'),

                TextInput::make('sku')
                    ->label('SKU de la Variante')
                    ->required()
                    ->maxLength(255)
                    ->helperText('SKU único para esta variante del producto'),

                TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01)
                    ->helperText('Precio específico para esta variante (opcional)'),

                TextInput::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Cantidad disponible de esta variante'),

                Toggle::make('is_default')
                    ->label('Variante por Defecto')
                    ->helperText('Marcar si esta es la variante principal del producto'),

                Textarea::make('description')
                    ->label('Descripción Específica')
                    ->rows(3)
                    ->helperText('Descripción específica para esta variante del producto'),

                KeyValue::make('attributes')
                    ->label('Atributos Específicos')
                    ->keyLabel('Atributo')
                    ->valueLabel('Valor')
                    ->helperText('Atributos específicos como talla, color, etc.'),

                FileUpload::make('image')
                    ->label('Imagen de la Variante')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('product-variants')
                    ->helperText('Imagen específica para esta variante'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
                   ->columns([
                       TextColumn::make('variantGroup.name')
                           ->label('Grupo')
                           ->searchable()
                           ->sortable()
                           ->weight('bold')
                           ->badge()
                           ->color(fn (string $state): string => match ($state) {
                               'Color' => 'green',
                               'Talla' => 'blue',
                               'Material' => 'orange',
                               'Estilo' => 'purple',
                               default => 'gray',
                           }),

                       TextColumn::make('name')
                           ->label('Variante')
                           ->searchable()
                           ->sortable()
                           ->weight('bold'),

                       TextColumn::make('description')
                           ->label('Descripción')
                           ->limit(30)
                           ->tooltip(function (TextColumn $column): ?string {
                               $state = $column->getState();
                               return strlen($state) > 30 ? $state : null;
                           }),

                       TextColumn::make('status')
                           ->label('Estado')
                           ->badge()
                           ->color(fn (string $state): string => match ($state) {
                               'active' => 'success',
                               'inactive' => 'danger',
                               default => 'gray',
                           }),
                   ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('variant_group_id')
                    ->label('Grupo de Variante')
                    ->relationship('variantGroup', 'name')
                    ->preload(),

                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activa',
                        'inactive' => 'Inactiva',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Asociar Variante')
                    ->form([
                        Select::make('variant_group_id')
                            ->label('Grupo de Variante')
                            ->options(function () {
                                return \App\Models\VariantGroup::active()
                                    ->ordered()
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('variant_id', null);
                            })
                            ->helperText('Selecciona el grupo de variante (Color, Talla, Material, etc.)'),

                        Select::make('variant_id')
                            ->label('Variante')
                            ->options(function (callable $get) {
                                $groupId = $get('variant_group_id');
                                if (!$groupId) {
                                    return [];
                                }
                                return \App\Models\Variant::where('variant_group_id', $groupId)
                                    ->where('status', 'active')
                                    ->ordered()
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Selecciona la variante específica del grupo elegido'),
                    ])
                    ->using(function (array $data) {
                        // Obtener el producto actual desde el RelationManager
                        $product = $this->getOwnerRecord();
                        $product->variants()->attach($data['variant_id']);
                    })
                    ->successNotificationTitle('Variante asociada exitosamente'),
            ])  
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
