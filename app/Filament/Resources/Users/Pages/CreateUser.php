<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
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
}
