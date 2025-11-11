<?php

namespace App\Filament\Exports;

use App\Models\Supplier;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;
use App\Enums\ProcType;
use Filament\Forms\Components\TextInput;

class SupplierExporter extends Exporter
{
    protected static ?string $model = Supplier::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('business_name')
                ->label('Business Name'),
            ExportColumn::make('website')
                ->label('Website'),
            ExportColumn::make('email')
                ->label('Email Address'),
            ExportColumn::make('mobile_number')
                ->label('Mobile #'),
            ExportColumn::make('landline_number')
                ->label('Landline #'),
            ExportColumn::make('owner_name')
                ->label('Owner'),
            ExportColumn::make('supplier_type')
                ->formatStateUsing(function ($state): string {
                    // $state will be the enum object if it's cast in your model
                    return $state instanceof ProcType ? $state->getLabel() : $state;
                })
                ->label('Type'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your supplier export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
