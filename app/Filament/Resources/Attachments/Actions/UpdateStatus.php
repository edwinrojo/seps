<?php

namespace App\Filament\Resources\Attachments\Actions;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class UpdateStatus
{
    public static function save($record, $data)
    {
        $user = request()->user() ?? Auth::user();

        $record->statuses()->create([
            'user_id' => $user->id,
            'status' => $data['status'],
            'status_date' => now(),
            'remarks' => $data['remarks'],
        ]);

        Notification::make()
            ->title('Success')
            ->body('Attachment status updated successfully.')
            ->success()
            ->send();
    }
}
