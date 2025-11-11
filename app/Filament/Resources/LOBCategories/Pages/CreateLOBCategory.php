<?php

namespace App\Filament\Resources\LOBCategories\Pages;

use App\Filament\Actions\SecureDeleteAction;
use App\Filament\Resources\LOBCategories\LOBCategoryResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateLOBCategory extends CreateRecord
{
    protected static string $resource = LOBCategoryResource::class;

    protected function getFormActions(): array
    {
        return [
            // ...parent::getFormActions(),
            $this->getCreateFormAction()
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Create Document'),
            $this->getCancelFormAction()
                ->icon(Heroicon::OutlinedXMark)
                ->label('Cancel'),
        ];
    }

}
