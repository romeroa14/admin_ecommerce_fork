<?php

namespace App\Filament\Resources\VariantGroups;

use App\Filament\Resources\VariantGroups\Pages\CreateVariantGroup;
use App\Filament\Resources\VariantGroups\Pages\EditVariantGroup;
use App\Filament\Resources\VariantGroups\Pages\ListVariantGroups;
use App\Filament\Resources\VariantGroups\Schemas\VariantGroupForm;
use App\Filament\Resources\VariantGroups\Tables\VariantGroupsTable;
use App\Models\VariantGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VariantGroupResource extends Resource
{
    protected static ?string $model = VariantGroup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|UnitEnum|null $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Grupos de Variantes';

    protected static ?string $modelLabel = 'Grupo de Variante';

    protected static ?string $pluralModelLabel = 'Grupos de Variantes';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VariantGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VariantGroupsTable::configure($table);
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
            'index' => ListVariantGroups::route('/'),
            'create' => CreateVariantGroup::route('/create'),
            'edit' => EditVariantGroup::route('/{record}/edit'),
        ];
    }
}
