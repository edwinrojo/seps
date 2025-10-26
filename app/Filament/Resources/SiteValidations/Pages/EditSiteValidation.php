<?php

namespace App\Filament\Resources\SiteValidations\Pages;

use App\Filament\GlobalActions\SecureDeleteAction;
use App\Filament\GlobalActions\SiteImageAction;
use App\Filament\Resources\SiteValidations\SiteValidationResource;
use App\Models\Address;
use App\Models\SiteValidation;
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Transform the relationship data to match the Repeater structure
        $data['validation_purposes'] = $this->record->validation_purposes()->get()->map(function ($purpose) {
            if ($purpose->is_iv) {
                $latestStatus = Address::find($this->record->address_id)->statuses()->latest()->first();
                return [
                    'validation_purpose_id' => $purpose->id,
                    'status' => $latestStatus ? ($latestStatus->status->value == 'validated' ? 'approve' : 'reject') : null,
                    'status_remarks' => $latestStatus->remarks ?? null,
                ];
            }
            return [
                'validation_purpose_id' => $purpose->id,
                'status' => $purpose->pivot->status ?? null,
                'status_remarks' => $purpose->pivot->remarks ?? null,
            ];
        })->toArray();

        return $data;
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extract validation_purposes before updating the model
        $validationPurposes = $data['validation_purposes'] ?? [];
        unset($data['validation_purposes']);

        $record->update($data);

        // Extract validation_purpose_id from each repeater item
        $purposeIds = collect($validationPurposes)->pluck('validation_purpose_id')->filter()->toArray();
        $record->validation_purposes()->sync($purposeIds);

        // Handle address validation status
        foreach ($validationPurposes as $purposeData) {
            if (!isset($purposeData['status'])) {
                continue;
            }
            if (isset($purposeData['status'])) {
                $latestStatus = Address::find($this->record->address_id)->statuses()->latest()->first();
                if ($latestStatus) {
                    // Update existing status
                    $latestStatus->update([
                        'user_id' => request()->user()->id,
                        'status' => $purposeData['status'] == 'approve' ? 'validated' : 'rejected',
                        'remarks' => $purposeData['status_remarks'] ?? null,
                        'status_date' => now(),
                    ]);
                    continue;
                }
                // Create new status
                $record->address->statuses()->create([
                    'user_id' => request()->user()->id,
                    'status' => $purposeData['status'] == 'approve' ? 'validated' : 'rejected',
                    'remarks' => $purposeData['status_remarks'] ?? null,
                    'status_date' => now(),
                ]);
            }
        }

        SiteImageAction::saveMultiple($record, $data['site_images'] ?? null);

        return $record;
    }
}
