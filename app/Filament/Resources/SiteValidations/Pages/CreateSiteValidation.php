<?php

namespace App\Filament\Resources\SiteValidations\Pages;

use App\Filament\GlobalActions\SiteImageAction;
use App\Filament\Resources\SiteValidations\SiteValidationResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class CreateSiteValidation extends CreateRecord
{
    protected static string $resource = SiteValidationResource::class;

    protected function getFormActions(): array
    {
        return [
            // ...parent::getFormActions(),
            $this->getCreateFormAction()
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Create Site Validation'),
            $this->getCancelFormAction()
                ->icon(Heroicon::OutlinedXMark)
                ->label('Cancel'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['twg_id'] = request()->user()->id;

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $siteValidation = static::getModel()::create($data);

        SiteImageAction::saveMultiple($siteValidation, $data['site_images'] ?? null);

        return $siteValidation;
    }
}
