<?php

namespace App\Filament\Resources\Analytics\Pages;

use App\Filament\Resources\Analytics\AnalyticsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAnalytics extends ViewRecord
{
    protected static string $resource = AnalyticsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
