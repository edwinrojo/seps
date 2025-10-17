<?php

namespace App\Filament\Resources\Suppliers\Actions;

use App\Enums\Status;
use App\Models\Attachment;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class AttachDocument
{
    public static function handle($supplier, $record, $data)
    {
        $attachment = $supplier->attachments()->where('document_id', $record->id)->first();
        if (!$attachment) {
            // Create new pivot record
            $supplier->attachments()->create([
                'document_id' => $record->id,
                'file_path' => $data['file_path'],
                'validity_date' => $data['validity_date'],
                'file_size' => $data['file_size'],
            ]);
        } else {
            // Remove existing file from storage
            Storage::disk('public')->delete($attachment->file_path);
            // Update existing record
            $attachment->update([
                'file_path' => $data['file_path'],
                'validity_date' => $data['validity_date'],
                'file_size' => $data['file_size'],
            ]);
        }

        self::createStatus($supplier, $record, $data);

        Notification::make()
            ->title('Document attached successfully')
            ->success()
            ->send();
    }

    public static function createStatus($supplier, $record, $data)
    {
        $attachment = $supplier->attachments()->where('document_id', $record->id)->first();
        $remarks = match (request()->user()->role->value) {
            'administrator' => 'uploaded and validated by an administrator',
            'supplier' => 'uploaded for review',
            default => 'updated',
        };
        $attachment->statuses()->create([
            'user_id' => request()->user()->id,
            'status' => request()->user()->role->value === 'administrator' ? Status::Validated : Status::PendingReview,
            'statusable_type' => Attachment::class,
            'statusable_id' => $attachment->id,
            'remarks' => '<p>New document attached on ' . now()->format('F d, Y') . ' with file name "' . basename($data['file_path']) . '" is ' . $remarks . '.</p>',
            'status_date' => now(),
        ]);
    }
}
