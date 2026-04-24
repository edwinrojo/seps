<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\AnalyticsSnapshot;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class SiteValidationsChart extends ChartWidget
{
    use HasFiltersSchema;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Site Validations Overview';

    protected ?string $description = 'A quick overview of the total number of site validations conducted each month.';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return request()->user()->role === UserRole::Administrator || request()->user()->role === UserRole::Twg;
    }

    protected function getData(): array
    {
        $year = (int) ($this->filters['year'] ?? now()->year);

        // Get monthly validation counts from snapshots for the selected year
        $monthlyData = $this->getMonthlyValidationData($year);

        return [
            'datasets' => [
                [
                    'label' => 'Site Validations',
                    'data' => $monthlyData,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                    ],
                    'borderColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('year')
                ->default(now()->year)
                ->numeric()
                ->minValue(2000)
                ->maxValue(2030),
        ]);
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Get monthly validation data from snapshots for a given year.
     */
    private function getMonthlyValidationData(int $year): array
    {
        $monthlyData = array_fill(0, 12, 0);

        // Get all snapshots for this year where metric is validations.this_month
        $snapshots = AnalyticsSnapshot::where('metric_key', 'validations.this_month')
            ->whereBetween('snapshot_date', [
                now()->setYear($year)->startOfYear()->toDateString(),
                now()->setYear($year)->endOfYear()->toDateString(),
            ])
            ->orderBy('snapshot_date', 'desc')
            ->get();

        // Group by month and take the latest value for each month
        $monthValues = [];
        foreach ($snapshots as $snapshot) {
            $month = $snapshot->snapshot_date->month - 1; // 0-indexed
            if (! isset($monthValues[$month])) {
                $monthValues[$month] = (int) $snapshot->metric_value;
            }
        }

        // Fill the monthly data array with snapshot values
        foreach ($monthValues as $month => $value) {
            $monthlyData[$month] = $value;
        }

        return $monthlyData;
    }
}
