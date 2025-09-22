<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

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
