<?php

use App\Enums\UserRole;
use App\Filament\Widgets\DocumentsOverview;
use App\Models\AnalyticsSnapshot;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

function documentsOverviewTrendData(string $metricKey): array
{
    $widget = new class extends DocumentsOverview
    {
        public function exposeTrendData(string $metricKey): array
        {
            return $this->getTrendData($metricKey);
        }
    };

    return $widget->exposeTrendData($metricKey);
}

it('displays document stats with snapshot data', function () {
    // Seed snapshots for the last 12 days
    for ($i = 0; $i < 12; $i++) {
        $date = now()->subDays($i);
        AnalyticsSnapshot::create([
            'snapshot_date' => $date,
            'metric_key' => 'documents.total',
            'metric_value' => 50 + ($i * 5),
        ]);
    }

    $user = User::factory()->create(['role' => UserRole::Administrator]);

    Livewire::actingAs($user)
        ->test(DocumentsOverview::class)
        ->assertSeeHtml('Total Documents');
});

it('retrieves trend data from document snapshots', function () {
    for ($i = 0; $i < 12; $i++) {
        $date = now()->subDays($i);
        AnalyticsSnapshot::create([
            'snapshot_date' => $date,
            'metric_key' => 'documents.total',
            'metric_value' => 20 + $i,
        ]);
    }

    $trendData = documentsOverviewTrendData('documents.total');

    expect($trendData)
        ->toHaveCount(12)
        ->and($trendData[0])->toBe(31.0)
        ->and($trendData[11])->toBe(20.0);
});
