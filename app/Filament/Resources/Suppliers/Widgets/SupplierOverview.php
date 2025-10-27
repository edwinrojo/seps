<?php

namespace App\Filament\Resources\Suppliers\Widgets;

use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SupplierOverview extends StatsOverviewWidget
{

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
                ->color('warning')
                ->description('Suppliers marked as ineligible'),
        ];
    }

    protected function getFullyValidatedSuppliersCount(): int
    {
        $count = 0;
        \App\Models\Supplier::with([
            'addresses.statuses',
            'attachments.statuses',
        ])->chunkById(200, function ($suppliers) use (&$count) {
            foreach ($suppliers as $supplier) {
                $supplierStatus = new \App\Helpers\SupplierStatus($supplier);
                if ($supplierStatus->isFullyValidated()) {
                    $count++;
                }
            }
        });

        return $count;
    }
}
