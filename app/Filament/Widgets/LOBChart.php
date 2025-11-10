<?php

namespace App\Filament\Widgets;

use App\Models\LobCategory;
use Filament\Widgets\ChartWidget;

class LOBChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected ?string $heading = 'Line of Business Overview';
    protected ?string $description = 'A quick overview of the distribution of suppliers across different lines of business.';
    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Line of Business',
                    'data' => LobCategory::query()
                        ->withCount('supplierLobs')
                        ->get()
                        ->map(fn (LobCategory $lob) => $lob->supplier_lobs_count)
                        ->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => LobCategory::query()
                ->get()
                ->map(fn (LobCategory $lob) => $lob->title)
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
