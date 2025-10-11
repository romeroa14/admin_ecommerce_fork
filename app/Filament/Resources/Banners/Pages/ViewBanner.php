<?php

namespace App\Filament\Resources\Banners\Pages;

use App\Filament\Resources\Banners\BannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBanner extends ViewRecord
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
