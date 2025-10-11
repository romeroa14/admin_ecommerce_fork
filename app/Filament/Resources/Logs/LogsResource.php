<?php

namespace App\Filament\Resources\Logs;

use App\Filament\Resources\Logs\Pages\CreateLogs;
use App\Filament\Resources\Logs\Pages\EditLogs;
use App\Filament\Resources\Logs\Pages\ListLogs;
use App\Filament\Resources\Logs\Pages\ViewLogs;
use App\Filament\Resources\Logs\Schemas\LogsForm;
use App\Filament\Resources\Logs\Tables\LogsTable;
use App\Models\Log;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LogsResource extends Resource
{
    protected static ?string $model = Log::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'action';

    protected static UnitEnum|string|null $navigationGroup = 'Administración';

    protected static ?string $navigationLabel = 'Logs';

    protected static ?string $modelLabel = 'Log';

    protected static ?string $pluralModelLabel = 'Logs';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return LogsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogsTable::configure($table);
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
            'index' => ListLogs::route('/'),
            'create' => CreateLogs::route('/create'),
            'view' => ViewLogs::route('/{record}'),
            'edit' => EditLogs::route('/{record}/edit'),
        ];
    }
}
