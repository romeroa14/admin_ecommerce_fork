<?php

namespace App\Filament\Resources\Shipments\Pages;

use App\Filament\Resources\Shipments\ShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewShipment extends ViewRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
