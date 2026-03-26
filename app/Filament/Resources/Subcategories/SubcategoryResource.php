<?php

namespace App\Filament\Resources\Subcategories;

use App\Filament\Resources\Subcategories\Pages\CreateSubcategory;
use App\Filament\Resources\Subcategories\Pages\EditSubcategory;
use App\Filament\Resources\Subcategories\Pages\ListSubcategories;
use App\Filament\Resources\Subcategories\Schemas\SubcategoryForm;
use App\Filament\Resources\Subcategories\Tables\SubcategoriesTable;
use App\Models\Subcategory;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubcategoryResource extends Resource
{
    protected static ?string $model = Subcategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $recordTitleAttribute = 'name';

    protected static UnitEnum|string|null $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Subcategorías';

    protected static ?string $modelLabel = 'Subcategoría';

    protected static ?string $pluralModelLabel = 'Subcategorías';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SubcategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubcategoriesTable::configure($table);
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
            'index' => ListSubcategories::route('/'),
            'create' => CreateSubcategory::route('/create'),
            'edit' => EditSubcategory::route('/{record}/edit'),
        ];
    }
}
