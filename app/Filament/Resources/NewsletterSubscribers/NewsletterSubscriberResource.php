<?php

namespace App\Filament\Resources\NewsletterSubscribers;

use App\Filament\Resources\NewsletterSubscribers\Pages\CreateNewsletterSubscriber;
use App\Filament\Resources\NewsletterSubscribers\Pages\EditNewsletterSubscriber;
use App\Filament\Resources\NewsletterSubscribers\Pages\ListNewsletterSubscribers;
use App\Filament\Resources\NewsletterSubscribers\Pages\ViewNewsletterSubscriber;
use App\Filament\Resources\NewsletterSubscribers\Schemas\NewsletterSubscriberForm;
use App\Filament\Resources\NewsletterSubscribers\Tables\NewsletterSubscribersTable;
use App\Models\NewsletterSubscriber;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'email';

    // protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Newsletter';

    protected static ?string $modelLabel = 'Suscriptor';

    protected static ?string $pluralModelLabel = 'Suscriptores';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return NewsletterSubscriberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsletterSubscribersTable::configure($table);
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
            'index' => ListNewsletterSubscribers::route('/'),
            'create' => CreateNewsletterSubscriber::route('/create'),
            'view' => ViewNewsletterSubscriber::route('/{record}'),
            'edit' => EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }
}
