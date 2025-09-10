<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Actions\SecureDeleteAction;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::OutlinedEye)
                ->label('View User'),
            SecureDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
            ForceDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
            RestoreAction::make()
                ->icon(Heroicon::OutlinedArrowUturnLeft),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Save User'),
            $this->getCancelFormAction()
                ->icon(Heroicon::OutlinedXMark)
                ->label('Cancel'),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        switch ($record->role->value) {
            case 'twg':
                $record->twg()->update([
                    'office_id' => $data['twg']['office_id'],
                    'position_title' => $data['twg']['position_title'],
                    'twg_type' => $data['twg']['twg_type'],
                ]);
                break;
            case 'end-user':
                $record->syncRoles(['staff']);
                break;
            default:
                break;
        }

        return $record;
    }
}
