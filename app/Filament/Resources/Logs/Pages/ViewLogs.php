<?php

namespace App\Filament\Resources\Logs\Pages;

use App\Filament\Resources\Logs\LogsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLogs extends ViewRecord
{
    protected static string $resource = LogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
