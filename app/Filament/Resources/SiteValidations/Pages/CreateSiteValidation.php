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
        // Extract validation_purposes before creating the model
        $validationPurposes = $data['validation_purposes'] ?? [];
        unset($data['validation_purposes']);

        $siteValidation = static::getModel()::create($data);

        // Extract validation_purpose_id from each repeater item
        $purposeIds = collect($validationPurposes)->pluck('validation_purpose_id')->filter()->toArray();
        $siteValidation->validation_purposes()->sync($purposeIds);

        // Handle address validation status for IV purposes
        foreach ($validationPurposes as $purposeData) {
            if (isset($purposeData['validation_purpose_id'])) {
                $purpose = $siteValidation->validation_purposes()->where('id', $purposeData['validation_purpose_id'])->first();
                if ($purpose && $purpose->is_iv) {
                    $address = $siteValidation->address;
                    $statusValue = $purposeData['status'] === 'approve' ? 'validated' : 'rejected';
                    $address->statuses()->create([
                        'user_id' => request()->user()->id,
                        'status' => $statusValue,
                        'remarks' => $purposeData['status_remarks'] ?? null,
                        'status_date' => now(),
                    ]);
                }
            }
        }

        SiteImageAction::saveMultiple($siteValidation, $data['site_images'] ?? null);

        return $siteValidation;
    }
}
