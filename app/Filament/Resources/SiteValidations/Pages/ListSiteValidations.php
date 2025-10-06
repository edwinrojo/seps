<?php

namespace App\Filament\Resources\SiteValidations\Pages;

use App\Filament\Resources\SiteValidations\SiteValidationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListSiteValidations extends ListRecords
{
    protected static string $resource = SiteValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Record')
                ->icon(Heroicon::OutlinedPlus)
        ];
    }
}
