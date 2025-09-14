<?php

namespace App\Filament\Resources\LOBCategories\Pages;

use App\Filament\Resources\LOBCategories\LOBCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListLOBCategories extends ListRecords
{
    protected static string $resource = LOBCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Line of Business')
                ->icon(Heroicon::Plus),
        ];
    }
}
