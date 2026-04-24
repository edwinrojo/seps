<?php

namespace App\Notifications;

use App\Mail\DocumentExpiredMail;
use App\Models\Attachment;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class DocumentExpired extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Attachment $attachment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): Mailable
    {
        return (new DocumentExpiredMail($this->attachment))
            ->to($notifiable->email);
    }

    public function toDatabase(object $notifiable): array
    {
        $validityDate = $this->attachment->validity_date
            ? Carbon::parse($this->attachment->validity_date)->format('F d, Y')
            : 'N/A';

        return FilamentNotification::make()
            ->title('Document Expired')
            ->body("Your {$this->attachment->document->title} document has expired on {$validityDate}.")
            ->warning()
            ->actions([
                Action::make('viewProfile')
                    ->label('View Profile')
                    ->url(route('filament.supplier.pages.business-profile'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
