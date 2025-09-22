<?php

namespace App\Filament\Resources\SiteValidations\Pages;

use App\Filament\Resources\SiteValidations\SiteValidationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSiteValidation extends ViewRecord
{
    protected static string $resource = SiteValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
