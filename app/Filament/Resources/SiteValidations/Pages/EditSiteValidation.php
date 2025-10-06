<?php

namespace App\Filament\Resources\SiteValidations\Pages;

use App\Filament\GlobalActions\SecureDeleteAction;
use App\Filament\GlobalActions\SiteImageAction;
use App\Filament\Resources\SiteValidations\SiteValidationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditSiteValidation extends EditRecord
{
    protected static string $resource = SiteValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Save Record'),
            SecureDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
            $this->getCancelFormAction()
                ->icon(Heroicon::OutlinedXMark)
                ->label('Cancel'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        SiteImageAction::saveMultiple($record, $data['site_images'] ?? null);

        return $record;
    }
}
