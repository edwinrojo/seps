<?php

namespace App\Console\Commands;

use App\Enums\Status as DocumentStatus;
use App\Enums\UserRole;
use App\Helpers\SupplierNotificationRecipient;
use App\Models\Attachment;
use App\Models\User;
use App\Notifications\DocumentExpired;
use App\Settings\SiteSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessAttachmentValidity extends Command
{
    protected $signature = 'attachments:process-validity';

    protected $description = 'Mark expired attachments and send expiry notifications';

    public function handle(): int
    {
        $expiredAttachments = $this->getAttachmentsWithLatestNonExpiredStatusBeforeDate(now()->startOfDay());

        $systemAdministrator = User::query()
            ->where('role', UserRole::Administrator->value)
            ->first();

        if (! $systemAdministrator) {
            $this->warn('No system administrator found. Skipping attachment validity processing.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($expiredAttachments, $systemAdministrator): void {
            foreach ($expiredAttachments as $attachment) {
                $attachment->statuses()->create([
                    'user_id' => $systemAdministrator->id,
                    'status' => DocumentStatus::Expired->value,
                    'remarks' => '<p>The document has expired based on the validity date</p>',
                    'status_date' => now(),
                ]);
            }
        });

        $this->sendExpiredDocumentNotifications($expiredAttachments);
        $this->sendExpiryReminderNotifications();
        $this->sendAdministratorSummaryNotification($systemAdministrator, $expiredAttachments->count());

        Log::info('Expired attachment scheduler ran', [
            'processed' => $expiredAttachments->count(),
        ]);

        $this->info("Processed {$expiredAttachments->count()} expired attachment(s).");

        return self::SUCCESS;
    }

    private function sendExpiredDocumentNotifications(Collection $expiredAttachments): void
    {
        foreach ($expiredAttachments as $attachment) {
            $supplierUser = $attachment->supplier
                ? SupplierNotificationRecipient::resolve($attachment->supplier)
                : null;

            if (! $supplierUser || ! $attachment->document) {
                continue;
            }

            $supplierUser->notify(new DocumentExpired($attachment));
        }
    }

    private function sendExpiryReminderNotifications(): void
    {
        $numberOfDays = app(SiteSettings::class)->document_expiry_notification_days;

        if ($numberOfDays === null) {
            return;
        }

        $attachmentsExpiringSoon = $this->getAttachmentsWithLatestNonExpiredStatusOnDate(
            now()->addDays((int) $numberOfDays)->startOfDay()
        );

        foreach ($attachmentsExpiringSoon as $attachment) {
            $supplierUser = $attachment->supplier
                ? SupplierNotificationRecipient::resolve($attachment->supplier)
                : null;

            if (! $supplierUser || ! $attachment->document) {
                continue;
            }

            $supplierUser->notify(
                Notification::make()
                    ->title('Document Expiry Reminder')
                    ->body('Your document "'.$attachment->document->title.'" is set to expire on '.Carbon::parse($attachment->validity_date)->toFormattedDateString().'. Please take the necessary actions to renew it.')
                    ->warning()
                    ->toDatabase(),
            );
        }
    }

    private function sendAdministratorSummaryNotification(User $systemAdministrator, int $count): void
    {
        if ($count < 1) {
            return;
        }

        $systemAdministrator->notify(
            Notification::make()
                ->title('Document Expiration')
                ->body($count.' document/s have expired today. '.now()->toDateString())
                ->danger()
                ->actions([
                    Action::make('view')
                        ->label('View Documents')
                        ->url('/admin/attachments?tab=expired')
                        ->markAsRead()
                        ->button(),
                ])
                ->toDatabase(),
        );
    }

    private function getAttachmentsWithLatestNonExpiredStatusBeforeDate(Carbon $date): Collection
    {
        return Attachment::query()
            ->whereDate('validity_date', '<', $date)
            ->whereHas('statuses', function ($query): void {
                $query->where('id', function ($sub): void {
                    $sub->selectRaw('MAX(id)')
                        ->from('statuses as s2')
                        ->whereColumn('s2.statusable_id', 'statuses.statusable_id')
                        ->whereColumn('s2.statusable_type', 'statuses.statusable_type');
                })->where('status', '!=', DocumentStatus::Expired->value);
            })
            ->get();
    }

    private function getAttachmentsWithLatestNonExpiredStatusOnDate(Carbon $date): Collection
    {
        return Attachment::query()
            ->whereDate('validity_date', '=', $date)
            ->whereHas('statuses', function ($query): void {
                $query->where('id', function ($sub): void {
                    $sub->selectRaw('MAX(id)')
                        ->from('statuses as s2')
                        ->whereColumn('s2.statusable_id', 'statuses.statusable_id')
                        ->whereColumn('s2.statusable_type', 'statuses.statusable_type');
                })->where('status', '!=', DocumentStatus::Expired->value);
            })
            ->get();
    }
}
