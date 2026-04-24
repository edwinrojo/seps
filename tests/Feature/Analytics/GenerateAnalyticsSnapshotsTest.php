<?php

use App\Enums\ProcType;
use App\Models\AnalyticsSnapshot;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('generates analytics snapshots command runs successfully', function () {
    $this->artisan('analytics:generate-snapshots', ['--date' => '2026-03-20'])
        ->assertSuccessful();

    expect(AnalyticsSnapshot::where('snapshot_date', '2026-03-20')->count())
        ->toBeGreaterThan(0);
});

it('generates supplier metrics snapshots', function () {
    Supplier::query()->insert([
        ['id' => 'supplier-1', 'business_name' => 'Supplier 1', 'email' => 'supplier1@test.com', 'mobile_number' => '09170000001', 'supplier_type' => ProcType::GOODS->value],
        ['id' => 'supplier-2', 'business_name' => 'Supplier 2', 'email' => 'supplier2@test.com', 'mobile_number' => '09170000002', 'supplier_type' => ProcType::GOODS->value],
        ['id' => 'supplier-3', 'business_name' => 'Supplier 3', 'email' => 'supplier3@test.com', 'mobile_number' => '09170000003', 'supplier_type' => ProcType::CONSULTING_SERVICES->value],
        ['id' => 'supplier-4', 'business_name' => 'Supplier 4', 'email' => 'supplier4@test.com', 'mobile_number' => '09170000004', 'supplier_type' => ProcType::GOODS->value],
        ['id' => 'supplier-5', 'business_name' => 'Supplier 5', 'email' => 'supplier5@test.com', 'mobile_number' => '09170000005', 'supplier_type' => ProcType::INFRASTRUCTURE->value],
    ]);

    $this->artisan('analytics:generate-snapshots', ['--date' => '2026-03-20'])
        ->assertSuccessful();

    expect(AnalyticsSnapshot::where('metric_key', 'suppliers.total')->first())
        ->metric_value->toEqual('5.00')
        ->snapshot_date->toEqual(Carbon::parse('2026-03-20'));
});

it('upserts snapshots on subsequent runs for same date', function () {
    Supplier::query()->insert([
        ['id' => 'supplier-1', 'business_name' => 'Supplier 1', 'email' => 'supplier1@test.com', 'mobile_number' => '09170000001', 'supplier_type' => ProcType::GOODS->value],
        ['id' => 'supplier-2', 'business_name' => 'Supplier 2', 'email' => 'supplier2@test.com', 'mobile_number' => '09170000002', 'supplier_type' => ProcType::GOODS->value],
        ['id' => 'supplier-3', 'business_name' => 'Supplier 3', 'email' => 'supplier3@test.com', 'mobile_number' => '09170000003', 'supplier_type' => ProcType::CONSULTING_SERVICES->value],
    ]);

    $this->artisan('analytics:generate-snapshots', ['--date' => '2026-03-20'])->assertSuccessful();

    $initial = AnalyticsSnapshot::where('metric_key', 'suppliers.total')->first()->metric_value;
    expect($initial)->toEqual('3.00');

    Supplier::query()->insert([
        ['id' => 'supplier-4', 'business_name' => 'Supplier 4', 'email' => 'supplier4@test.com', 'mobile_number' => '09170000004', 'supplier_type' => ProcType::GOODS->value],
        ['id' => 'supplier-5', 'business_name' => 'Supplier 5', 'email' => 'supplier5@test.com', 'mobile_number' => '09170000005', 'supplier_type' => ProcType::INFRASTRUCTURE->value],
    ]);

    $this->artisan('analytics:generate-snapshots', ['--date' => '2026-03-20'])->assertSuccessful();

    $updated = AnalyticsSnapshot::where('metric_key', 'suppliers.total')->first()->metric_value;

    expect($updated)->toEqual('5.00')->not->toEqual($initial);
    expect(AnalyticsSnapshot::where('metric_key', 'suppliers.total')->count())->toBe(1);
});

it('respects the date option parameter', function () {
    Supplier::query()->insert([
        ['id' => 'supplier-1', 'business_name' => 'Supplier 1', 'email' => 'supplier1@test.com', 'mobile_number' => '09170000001', 'supplier_type' => ProcType::GOODS->value],
        ['id' => 'supplier-2', 'business_name' => 'Supplier 2', 'email' => 'supplier2@test.com', 'mobile_number' => '09170000002', 'supplier_type' => ProcType::GOODS->value],
    ]);

    $specificDate = '2026-02-15';

    $this->artisan('analytics:generate-snapshots', ['--date' => $specificDate])
        ->assertSuccessful();

    expect(AnalyticsSnapshot::where('snapshot_date', $specificDate)->count())
        ->toBeGreaterThan(0);
});
