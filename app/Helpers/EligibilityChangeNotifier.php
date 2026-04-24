<?php

namespace App\Helpers;

use App\Models\Supplier;
use App\Notifications\EligibilityChanged;

class EligibilityChangeNotifier
{
    public static function currentEligibility(Supplier $supplier): bool
    {
        $refreshedSupplier = Supplier::query()
            ->with([
                'user',
                'addresses.statuses',
                'attachments.statuses',
                'supplierLobs',
                'lob_statuses',
            ])
            ->find($supplier->id);

        if (! $refreshedSupplier) {
            return false;
        }

        $supplierStatus = new SupplierStatus($refreshedSupplier);

        return $supplierStatus->isFullyValidated();
    }

    public static function notifyIfChanged(Supplier $supplier, bool $previousEligibility): void
    {
        $refreshedSupplier = Supplier::query()
            ->with([
                'user',
                'addresses.statuses',
                'attachments.statuses',
                'supplierLobs',
                'lob_statuses',
            ])
            ->find($supplier->id);

        if (! $refreshedSupplier) {
            return;
        }

        $supplierUser = SupplierNotificationRecipient::resolve($refreshedSupplier);

        if (! $supplierUser) {
            return;
        }

        $supplierStatus = new SupplierStatus($refreshedSupplier);
        $currentEligibility = $supplierStatus->isFullyValidated();

        if ($currentEligibility === $previousEligibility) {
            return;
        }

        $reasons = collect($supplierStatus->getLabels())
            ->filter(function (array $label) use ($currentEligibility): bool {
                if ($currentEligibility) {
                    return $label['color'] === 'success';
                }

                return $label['color'] !== 'success';
            })
            ->pluck('label')
            ->values()
            ->all();

        $supplierUser->notify(new EligibilityChanged($refreshedSupplier, $currentEligibility, $reasons));
    }
}
