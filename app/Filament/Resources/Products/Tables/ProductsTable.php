<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\User;
use App\Models\VariantGroup;
use App\Models\Variant;
use App\Helpers\CurrencyHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('SKU copiado')
                    ->copyMessageDuration(1500),

                ImageColumn::make('images')
                    ->label('Imagen')
                    ->square()
                    ->size(100)
                    ->defaultImageUrl(url('/images/placeholder-product.png'))
                    ->getStateUsing(function ($record) {
                        // Si hay imágenes, tomar la primera
                        if ($record->images && is_array($record->images) && count($record->images) > 0) {
                            $imagePath = $record->images[0];
                            // Construir la URL completa
                            return asset('storage/' . $imagePath);
                        }
                        return null;
                    }),

                TextColumn::make('name')
                    ->label('Nombre del Producto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                

                TextColumn::make('price')
                    ->label('Precio')
                    ->formatStateUsing(fn ($state) => CurrencyHelper::formatAmount($state))
                    ->sortable()
                    ->weight('bold'),

                

                TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state, Product $record): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= $record->low_stock_threshold => 'warning',
                        default => 'success',
                    }),

                

                

                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'draft',
                        'danger' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'draft' => 'Borrador',
                        'archived' => 'Archivado',
                        default => $state,
                    })
                    ->sortable(),

                

            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'draft' => 'Borrador',
                        'archived' => 'Archivado',
                    ])
                    ->multiple(),

                SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                
                TernaryFilter::make('is_featured')
                    ->label('Productos Destacados')
                    ->placeholder('Todos los productos')
                    ->trueLabel('Solo destacados')
                    ->falseLabel('No destacados'),

                TernaryFilter::make('track_inventory')
                    ->label('Rastrear Inventario')
                    ->placeholder('Todos los productos')
                    ->trueLabel('Rastreo habilitado')
                    ->falseLabel('Rastreo deshabilitado'),

                Filter::make('stock_status')
                    ->label('Estado del Stock')
                    ->form([
                        Select::make('stock_filter')
                            ->label('Filtrar por Stock')
                            ->options([
                                'in_stock' => 'En Stock',
                                'low_stock' => 'Stock Bajo',
                                'out_of_stock' => 'Sin Stock',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['stock_filter'] ?? null) {
                            'in_stock' => $query->where('stock', '>', 0),
                            'low_stock' => $query->whereColumn('stock', '<=', 'low_stock_threshold'),
                            'out_of_stock' => $query->where('stock', '<=', 0),
                            default => $query,
                        };
                    }),

                Filter::make('price_range')
                    ->label('Rango de Precios')
                    ->form([
                        TextInput::make('min_price')
                            ->label('Precio Mínimo')
                            ->numeric()
                            ->prefix(fn () => CurrencyHelper::getCurrentCurrencySymbol())
                            ->helperText(fn () => 'Moneda: ' . CurrencyHelper::getCurrentCurrencyCode()),
                        TextInput::make('max_price')
                            ->label('Precio Máximo')
                            ->numeric()
                            ->prefix(fn () => CurrencyHelper::getCurrentCurrencySymbol())
                            ->helperText(fn () => 'Moneda: ' . CurrencyHelper::getCurrentCurrencyCode()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['max_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('add_to_cart')
                    ->label('Agregar al Carrito')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->form([
                        Select::make('user_id')
                            ->label('Usuario')
                            ->options(User::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(1),
                        Repeater::make('variants')
                            ->label('Variantes')
                            ->schema([
                                Select::make('variant_group_id')
                                    ->label('Grupo de Variante')
                                    ->options(VariantGroup::all()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('variant_id', null);
                                    }),
                                Select::make('variant_id')
                                    ->label('Variante')
                                    ->options(function (callable $get) {
                                        $groupId = $get('variant_group_id');
                                        if (!$groupId) {
                                            return [];
                                        }
                                        return Variant::where('variant_group_id', $groupId)
                                            ->where('status', 'active')
                                            ->get()
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                $state['variant_group_id'] && $state['variant_id'] 
                                    ? "Grupo: {$state['variant_group_id']} - Variante: {$state['variant_id']}"
                                    : null
                            ),
                    ])
                    ->action(function (Product $record, array $data): void {
                        // Buscar o crear carrito para el usuario
                        $cart = Cart::firstOrCreate(
                            ['user_id' => $data['user_id']],
                            [
                                'session_id' => 'session_' . $data['user_id'],
                                'subtotal' => 0,
                                'discount_amount' => 0,
                                'tax_amount' => 0,
                                'total' => 0,
                                'expires_at' => now()->addDays(7),
                            ]
                        );
                        
                        // Procesar variantes
                        $variants = [];
                        foreach ($data['variants'] ?? [] as $variantData) {
                            if ($variantData['variant_group_id'] && $variantData['variant_id']) {
                                $variantGroup = VariantGroup::find($variantData['variant_group_id']);
                                $variant = Variant::find($variantData['variant_id']);
                                if ($variantGroup && $variant) {
                                    $variants[$variantGroup->name] = $variant->name;
                                }
                            }
                        }
                        
                        // Agregar producto al carrito con variantes
                        $cart->addProduct($record->id, $data['quantity'], $variants);
                        
                        Notification::make()
                            ->title('Producto agregado al carrito')
                            ->body("Se agregó {$data['quantity']} x {$record->name} al carrito de {$cart->user->name}")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Agregar al Carrito')
                    ->modalDescription('Selecciona el usuario, cantidad y variantes para agregar este producto al carrito.')
                    ->modalSubmitActionLabel('Agregar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
