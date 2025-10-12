<?php

namespace App\Filament\Resources\CartItems;

use App\Filament\Resources\CartItems\Pages\CreateCartItem;
use App\Filament\Resources\CartItems\Pages\EditCartItem;
use App\Filament\Resources\CartItems\Pages\ListCartItems;
use App\Filament\Resources\CartItems\Schemas\CartItemForm;
use App\Filament\Resources\CartItems\Tables\CartItemsTable;
use App\Models\CartItem;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CartItemResource extends Resource
{
    protected static ?string $model = CartItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $recordTitleAttribute = 'id';

    protected static UnitEnum|string|null $navigationGroup = 'Ventas';

    protected static ?string $navigationLabel = 'Items del Carrito';

    protected static ?string $modelLabel = 'Item del Carrito';

    protected static ?string $pluralModelLabel = 'Items del Carrito';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CartItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CartItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCartItems::route('/'),
            'create' => CreateCartItem::route('/create'),
            'edit' => EditCartItem::route('/{record}/edit'),
        ];
    }
}
