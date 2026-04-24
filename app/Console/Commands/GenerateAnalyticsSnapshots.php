<?php

namespace App\Console\Commands;

use App\Helpers\SupplierStatus;
use App\Models\AnalyticsSnapshot;
use App\Models\Attachment;
use App\Models\SiteValidation;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAnalyticsSnapshots extends Command
{
    protected $signature = 'analytics:generate-snapshots {--date= : The date to generate snapshots for (YYYY-MM-DD)}';

    protected $description = 'Generate daily analytics snapshots for KPI tracking';

    public function handle(): int
    {
        $snapshotDate = $this->option('date')
            ? Carbon::createFromFormat('Y-m-d', $this->option('date'))
            : now();

        $this->info("Generating analytics snapshots for {$snapshotDate->format('Y-m-d')}");

        // Supplier metrics
        $this->generateSupplierMetrics($snapshotDate);

        // Validation metrics
        $this->generateValidationMetrics($snapshotDate);

        // Document metrics
        $this->generateDocumentMetrics($snapshotDate);

        $this->info('Analytics snapshots generated successfully');

        return self::SUCCESS;
    }

    private function generateSupplierMetrics(Carbon $date): void
    {
        $totalSuppliers = Supplier::count();
        $eligibleCount = 0;

        Supplier::with([
            'addresses.statuses',
            'attachments.statuses',
            'lob_statuses',
        ])->chunkById(200, function ($suppliers) use (&$eligibleCount) {
            foreach ($suppliers as $supplier) {
                $supplierStatus = new SupplierStatus($supplier);
                if ($supplierStatus->isFullyValidated()) {
                    $eligibleCount++;
                }
            }
        });

        $ineligibleCount = $totalSuppliers - $eligibleCount;

        $this->upsertSnapshot($date, 'suppliers.total', $totalSuppliers);
        $this->upsertSnapshot($date, 'suppliers.eligible', $eligibleCount);
        $this->upsertSnapshot($date, 'suppliers.ineligible', $ineligibleCount);

        $this->line("  ✓ Supplier metrics: {$totalSuppliers} total, {$eligibleCount} eligible");
    }

    private function generateValidationMetrics(Carbon $date): void
    {
        $validationsThisMonth = SiteValidation::whereMonth('validation_date', $date->month)
            ->whereYear('validation_date', $date->year)
            ->count();

        $this->upsertSnapshot($date, 'validations.this_month', $validationsThisMonth);

        $this->line("  ✓ Validation metrics: {$validationsThisMonth} this month");
    }

    private function generateDocumentMetrics(Carbon $date): void
    {
        $totalDocuments = Attachment::count();

        $this->upsertSnapshot($date, 'documents.total', $totalDocuments);

        $this->line("  ✓ Document metrics: {$totalDocuments} total");
    }

    private function upsertSnapshot(Carbon $date, string $key, int|float $value, ?array $dimensions = null): void
    {
        AnalyticsSnapshot::upsert(
            [
                [
                    'snapshot_date' => $date->toDateString(),
                    'metric_key' => $key,
                    'metric_value' => $value,
                    'dimensions' => $dimensions ? json_encode($dimensions) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            uniqueBy: ['snapshot_date', 'metric_key'],
            update: ['metric_value' => $value, 'updated_at' => now()]
        );
    }
}
