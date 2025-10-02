<?php

namespace App\Filament\Resources\Attachments\Actions;

use Filament\Notifications\Notification;

class UpdateStatus
{
    public static function save($record, $data) {
        $record->statuses()->create([
            'user_id' => request()->user()->id,
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
