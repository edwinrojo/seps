<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Actions\SecureDeleteAction;
use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SecureDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Save Changes'),
            $this->getCancelFormAction()
                ->icon(Heroicon::OutlinedXMark)
                ->label('Cancel'),
        ];
    }
}
