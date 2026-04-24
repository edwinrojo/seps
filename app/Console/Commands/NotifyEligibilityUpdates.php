<?php

namespace App\Console\Commands;

use App\Helpers\SupplierNotificationRecipient;
use App\Helpers\SupplierStatus;
use App\Models\Supplier;
use App\Notifications\EligibilityChanged;
use Illuminate\Console\Command;

class NotifyEligibilityUpdates extends Command
{
    protected $signature = 'suppliers:notify-eligibility-updates';

    protected $description = 'Send database and email notifications when supplier eligibility status changes';

    public function handle(): int
    {
        $notifiedCount = 0;

        Supplier::query()
            ->with([
                'user',
                'addresses.statuses',
                'attachments.statuses',
                'supplierLobs',
                'lob_statuses',
            ])
            ->chunkById(200, function ($suppliers) use (&$notifiedCount): void {
                foreach ($suppliers as $supplier) {
                    $supplierUser = SupplierNotificationRecipient::resolve($supplier);

                    if (! $supplierUser) {
                        continue;
                    }

                    $supplierStatus = new SupplierStatus($supplier);
                    $isCurrentlyEligible = $supplierStatus->isFullyValidated();

                    $latestEligibilityNotification = $supplierUser->notifications()
                        ->where('type', EligibilityChanged::class)
                        ->latest()
                        ->first();

                    if ($latestEligibilityNotification !== null) {
                        $previousEligibility = (bool) data_get($latestEligibilityNotification->data, 'is_eligible');

                        if ($previousEligibility === $isCurrentlyEligible) {
                            continue;
                        }
                    }

                    $reasons = $this->buildReasons($supplierStatus, $isCurrentlyEligible);

                    $supplierUser->notify(new EligibilityChanged($supplier, $isCurrentlyEligible, $reasons));
                    $notifiedCount++;
                }
            });

        $this->info("Sent {$notifiedCount} eligibility update notification(s).");

        return self::SUCCESS;
    }

    private function buildReasons(SupplierStatus $supplierStatus, bool $isEligible): array
    {
        return collect($supplierStatus->getLabels())
            ->filter(function (array $label) use ($isEligible): bool {
                if ($isEligible) {
                    return $label['color'] === 'success';
                }

                return $label['color'] !== 'success';
            })
            ->pluck('label')
            ->values()
            ->all();
    }
}
