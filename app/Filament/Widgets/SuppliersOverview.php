<?php

namespace App\Filament\Widgets;

use App\Helpers\SupplierStatus;
use App\Models\Supplier;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuppliersOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected ?string $heading = 'Suppliers Overview';
    protected ?string $description = 'A quick overview of the total number of suppliers registered in the system.';
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Suppliers', Supplier::count())
                ->description('All registered suppliers in the system')
                ->descriptionIcon(Heroicon::OutlinedBriefcase, IconPosition::Before)
                ->color('primary')
                ->chart([7, 5, 8, 4, 6, 7, 9, 10, 6, 8, 7, 9]),
            Stat::make('Eligible Suppliers', $this->getFullyValidatedSuppliersCount())
                ->color('success')
                ->description('Suppliers marked as eligible')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle, IconPosition::Before)
                ->chart([7, 5, 8, 4, 6, 7, 9, 10, 6, 8, 7, 9]),
            Stat::make('Ineligible Suppliers', Supplier::count() - $this->getFullyValidatedSuppliersCount())
                ->color('warning')
                ->description('Suppliers marked as ineligible')
                ->descriptionIcon(Heroicon::OutlinedXCircle, IconPosition::Before)
                ->chart([7, 5, 8, 4, 6, 7, 9, 10, 6, 8, 7, 9]),
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }

    protected function getFullyValidatedSuppliersCount(): int
    {
        $count = 0;
        Supplier::with([
            'addresses.statuses',
            'attachments.statuses',
            'lob_statuses',
        ])->chunkById(200, function ($suppliers) use (&$count) {
            foreach ($suppliers as $supplier) {
                $supplierStatus = new SupplierStatus($supplier);
                if ($supplierStatus->isFullyValidated()) {
                    $count++;
                }
            }
        });

        return $count;
    }
}
