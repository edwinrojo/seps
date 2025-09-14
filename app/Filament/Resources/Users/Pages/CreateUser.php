<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make('1234');
        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            // ...parent::getFormActions(),
            $this->getCreateFormAction()
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Create User'),
            $this->getCancelFormAction()
                ->icon(Heroicon::OutlinedXMark)
                ->label('Cancel'),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $user = parent::handleRecordCreation($data);

        switch ($user->role->value) {
            case 'twg':
                $user->twg()->create([
                    'user_id' => $user->id,
                    'office_id' => $data['twg']['office_id'],
                    'position_title' => $data['twg']['position_title'],
                    'twg_type' => $data['twg']['twg_type'],
                ]);
                break;
            case 'end-user':
                $user->endUser()->create([
                    'user_id' => $user->id,
                    'office_id' => $data['endUser']['office_id'],
                    'designation' => $data['endUser']['designation'],
                ]);
                break;
            default:
                break;
        }

        return $user;
    }
}
