<?php

namespace App\Filament\Resources\SiteValidations\Pages;

use App\Filament\Resources\SiteValidations\SiteValidationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSiteValidation extends EditRecord
{
    protected static string $resource = SiteValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
