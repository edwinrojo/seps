<?php

use App\Enums\ProcType;
use App\Filament\Widgets\SuppliersOverview;
use App\Models\AnalyticsSnapshot;
use App\Models\Supplier;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

function suppliersOverviewTrendData(string $metricKey): array
{
    $widget = new class extends SuppliersOverview
    {
        public function exposeTrendData(string $metricKey): array
        {
            return $this->getTrendData($metricKey);
        }
    };

    return $widget->exposeTrendData($metricKey);
}

it('displays supplier stats with snapshot data', function () {
    // Create some suppliers
    Supplier::query()->insert([
        ['id' => 'supplier-1', 'business_name' => 'Supplier 1', 'email' => 'supplier1@test.com', 'mobile_number' => '09170000001', 'supplier_type' => ProcType::GOODS->value],
        ['id' => 'supplier-2', 'business_name' => 'Supplier 2', 'email' => 'supplier2@test.com', 'mobile_number' => '09170000002', 'supplier_type' => ProcType::GOODS->value],
        ['id' => 'supplier-3', 'business_name' => 'Supplier 3', 'email' => 'supplier3@test.com', 'mobile_number' => '09170000003', 'supplier_type' => ProcType::CONSULTING_SERVICES->value],
    ]);

    // Seed snapshots for the last 12 days
    for ($i = 0; $i < 12; $i++) {
        $date = now()->subDays($i);
        AnalyticsSnapshot::create([
            'snapshot_date' => $date,
            'metric_key' => 'suppliers.total',
            'metric_value' => 3 + $i,
        ]);
        AnalyticsSnapshot::create([
            'snapshot_date' => $date,
            'metric_key' => 'suppliers.eligible',
            'metric_value' => 2,
        ]);
        AnalyticsSnapshot::create([
            'snapshot_date' => $date,
            'metric_key' => 'suppliers.ineligible',
            'metric_value' => 1 + $i,
        ]);
    }

    Livewire::test(SuppliersOverview::class)
        ->assertSeeHtml('Total Suppliers')
        ->assertSeeHtml('Eligible Suppliers')
        ->assertSeeHtml('Ineligible Suppliers');
});

it('retrieves trend data from snapshots', function () {
    for ($i = 0; $i < 12; $i++) {
        $date = now()->subDays($i);
        AnalyticsSnapshot::create([
            'snapshot_date' => $date,
            'metric_key' => 'suppliers.total',
            'metric_value' => 10 + $i,
        ]);
    }

    $trendData = suppliersOverviewTrendData('suppliers.total');

    expect($trendData)
        ->toHaveCount(12)
        ->and($trendData[0])->toBe(21.0)
        ->and($trendData[11])->toBe(10.0);
});
