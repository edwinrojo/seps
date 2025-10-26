<?php

namespace App\Filament\Resources\Suppliers\Widgets;

use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SupplierOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Supplier Statistics';
    protected ?string $description = 'An overview of the total number of suppliers in the system.';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Suppliers', \App\Models\Supplier::count())
                ->color('primary')
                ->description('All registered suppliers'),
            Stat::make('Eligible Suppliers', $this->getFullyValidatedSuppliersCount())
                ->color('success')
                ->description('Suppliers marked as eligible'),
            Stat::make('Ineligible Suppliers', \App\Models\Supplier::count() - $this->getFullyValidatedSuppliersCount())
                ->color('danger')
                ->description('Suppliers marked as ineligible'),
        ];
    }

    protected function getFullyValidatedSuppliersCount(): int
    {
        $count = 0;

        // Process suppliers in chunks while eager-loading related data used by the
        // validation helpers to avoid N+1 queries and high memory usage.
        \App\Models\Supplier::with([
            'addresses.statuses',
            'attachments.statuses',
        ])->chunkById(200, function ($suppliers) use (&$count) {
            foreach ($suppliers as $supplier) {
                // SupplierStatus will operate on the already-loaded relations
                // (addresses, attachments and their statuses) so no additional
                // queries will be triggered per supplier here.
                $supplierStatus = new \App\Helpers\SupplierStatus($supplier);
                if ($supplierStatus->isFullyValidated()) {
                    $count++;
                }
            }
        });

        return $count;
    }
}
