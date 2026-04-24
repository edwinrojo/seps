<?php

use App\Enums\UserRole;
use App\Filament\Widgets\SiteValidationsChart;
use App\Models\AnalyticsSnapshot;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

it('displays site validations chart with snapshot data', function () {
    // Seed snapshots for each month of the current year
    $year = now()->year;
    for ($month = 1; $month <= 12; $month++) {
        // Create a snapshot on the last day of each month
        $date = now()->setYear($year)->setMonth($month)->endOfMonth();
        AnalyticsSnapshot::create([
            'snapshot_date' => $date,
            'metric_key' => 'validations.this_month',
            'metric_value' => $month * 10, // Jan: 10, Feb: 20, etc.
        ]);
    }

    $user = User::factory()->create(['role' => UserRole::Administrator]);

    Livewire::actingAs($user)
        ->test(SiteValidationsChart::class)
        ->assertSeeHtml('Site Validations Overview');
});

it('filters site validations by year', function () {
    $previousYear = now()->year - 1;

    // Add snapshots for previous year
    for ($month = 1; $month <= 12; $month++) {
        $date = now()->setYear($previousYear)->setMonth($month)->endOfMonth();
        AnalyticsSnapshot::create([
            'snapshot_date' => $date,
            'metric_key' => 'validations.this_month',
            'metric_value' => 5,
        ]);
    }

    // Add snapshots for current year
    $currentYear = now()->year;
    for ($month = 1; $month <= 3; $month++) {
        $date = now()->setYear($currentYear)->setMonth($month)->endOfMonth();
        AnalyticsSnapshot::create([
            'snapshot_date' => $date,
            'metric_key' => 'validations.this_month',
            'metric_value' => 15,
        ]);
    }

    $user = User::factory()->create(['role' => UserRole::Administrator]);

    Livewire::actingAs($user)
        ->test(SiteValidationsChart::class)
        ->set('filters', ['year' => $previousYear])
        ->assertSeeHtml('Site Validations Overview');
});
