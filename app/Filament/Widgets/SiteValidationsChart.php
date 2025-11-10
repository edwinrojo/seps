<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\SiteValidation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

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
        $year = $this->filters['year'] ?? null;
        $data = Trend::model(SiteValidation::class)
            ->dateColumn('validation_date')
            ->between(
                start: now()->setYear((int)$year)->startOfYear(),
                end: now()->setYear((int)$year)->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Site Validations',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
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
                    'borderWidth' => 1
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
                ->maxValue(2030)
        ]);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
