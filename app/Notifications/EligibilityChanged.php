<?php

namespace App\Notifications;

use App\Mail\EligibilityChangedMail;
use App\Models\Supplier;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class EligibilityChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Supplier $supplier,
        private readonly bool $isNowEligible,
        private readonly array $reasons = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): Mailable
    {
        return (new EligibilityChangedMail($this->supplier, $this->isNowEligible, $this->reasons))
            ->to($notifiable->email);
    }

    public function toDatabase(object $notifiable): array
    {
        $status = $this->isNowEligible ? 'now eligible' : 'now ineligible';
        $reasonsText = ! empty($this->reasons) ? ' Reasons: '.implode(', ', $this->reasons) : '';

        return FilamentNotification::make()
            ->title('Eligibility Status Changed')
            ->body("Your supplier account is {$status}.{$reasonsText}")
            ->color($this->isNowEligible ? 'success' : 'warning')
            ->actions([
                Action::make('viewProfile')
                    ->label('View Profile')
                    ->url(route('filament.supplier.pages.business-profile'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
