<?php

namespace App\Filament\Resources\LOBCategories\Pages;

use App\Filament\GlobalActions\SecureDeleteAction;
use App\Filament\Resources\LOBCategories\LOBCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditLOBCategory extends EditRecord
{
    protected static string $resource = LOBCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SecureDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash)
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Save Category'),
            $this->getCancelFormAction()
                ->icon(Heroicon::OutlinedXMark)
                ->label('Cancel'),
        ];
    }
}
