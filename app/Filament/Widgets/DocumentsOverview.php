<?php

namespace App\Filament\Widgets;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\AnalyticsSnapshot;
use App\Models\Attachment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DocumentsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Documents Overview';

    protected ?string $description = 'A quick overview of the total number of documents uploaded by suppliers.';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return request()->user()->role === UserRole::Administrator || request()->user()->role === UserRole::Twg;
    }

    protected function getStats(): array
    {
        $total = [
            Stat::make('Total Documents', Attachment::count())
                ->description('All uploaded documents by suppliers')
                ->descriptionIcon(Heroicon::OutlinedDocument, IconPosition::Before)
                ->color('primary')
                ->chart($this->getTrendData('documents.total')),

        ];

        return array_merge($total, $this->statusCharts());
    }

    public function statusCharts(): array
    {
        $statuses = Status::cases();
        $stats = [];
        foreach ($statuses as $status) {
            $statusCount = Attachment::query()->whereHas('statuses', function ($query) use ($status) {
                $query->where('status', $status->value)
                    ->whereRaw('statuses.id = (SELECT MAX(id) FROM statuses WHERE statuses.statusable_id = attachments.id)');
            })->count();

            array_push($stats,
                Stat::make($status->getLabel().' Documents', $statusCount)
                    ->description($status->getLabel().' documents uploaded by suppliers')
                    ->descriptionIcon($status->getOutlinedFilamentIcon(), IconPosition::Before)
                    ->color($status->getColor())
                    ->chart($this->getTrendData('documents.total'))
            );
        }

        return $stats;
    }

    public function getColumns(): int|array
    {
        return 5;
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
}
