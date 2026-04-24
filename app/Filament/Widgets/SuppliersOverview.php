<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsSnapshot;
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
        $totalSuppliers = Supplier::count();
        $eligibleSuppliers = $this->getEligibleSuppliersCount();
        $ineligibleSuppliers = $totalSuppliers - $eligibleSuppliers;

        return [
            Stat::make('Total Suppliers', $totalSuppliers)
                ->description('All registered suppliers in the system')
                ->descriptionIcon(Heroicon::OutlinedBriefcase, IconPosition::Before)
                ->color('primary')
                ->chart($this->getTrendData('suppliers.total')),
            Stat::make('Eligible Suppliers', $eligibleSuppliers)
                ->color('success')
                ->description('Suppliers marked as eligible')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle, IconPosition::Before)
                ->chart($this->getTrendData('suppliers.eligible')),
            Stat::make('Ineligible Suppliers', $ineligibleSuppliers)
                ->color('warning')
                ->description('Suppliers marked as ineligible')
                ->descriptionIcon(Heroicon::OutlinedXCircle, IconPosition::Before)
                ->chart($this->getTrendData('suppliers.ineligible')),
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }

    /**
     * Get trend data for the last 12 snapshots of a given metric.
     */
    protected function getTrendData(string $metricKey): array
    {
        $snapshots = AnalyticsSnapshot::query()
            ->fromSub(
                AnalyticsSnapshot::query()
                    ->select(['snapshot_date', 'metric_value'])
                    ->where('metric_key', $metricKey)
                    ->orderByDesc('snapshot_date')
                    ->limit(12),
                'latest_snapshots'
            )
            ->orderBy('snapshot_date')
            ->get();

        $values = $snapshots->pluck('metric_value')
            ->map(fn ($value) => (float) $value)
            ->values()
            ->toArray();

        if ($values === []) {
            return array_fill(0, 12, 0);
        }

        if (count($values) < 12) {
            $values = array_pad($values, 12, end($values));
        }

        return $values;
    }

    /**
     * Get the count of eligible suppliers using cached snapshots.
     */
    protected function getEligibleSuppliersCount(): int
    {
        $latestSnapshot = AnalyticsSnapshot::where('metric_key', 'suppliers.eligible')
            ->orderBy('snapshot_date', 'desc')
            ->first();

        return $latestSnapshot ? (int) $latestSnapshot->metric_value : 0;
    }
}
