<?php

namespace App\Filament\Widgets;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Helpers\SupplierStatus;
use App\Models\Attachment;
use App\Models\Supplier;
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
        $total =  [
            Stat::make('Total Documents', Attachment::count())
                ->description('All uploaded documents by suppliers')
                ->descriptionIcon(Heroicon::OutlinedDocument, IconPosition::Before)
                ->color('primary')
                ->chart([10, 12, 8, 15, 9, 14, 20, 18, 22, 19, 25, 30]),

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
                Stat::make($status->getLabel() . ' Documents', $statusCount)
                    ->description($status->getLabel() . ' documents uploaded by suppliers')
                    ->descriptionIcon(Heroicon::OutlinedDocument, IconPosition::Before)
                    ->color($status->getColor())
                    ->chart([10, 12, 8, 15, 9, 14, 20, 18, 22, 19, 25, 30])
            );
        }

        return $stats;
    }

    public function getColumns(): int | array
    {
        return 5;
    }
}
