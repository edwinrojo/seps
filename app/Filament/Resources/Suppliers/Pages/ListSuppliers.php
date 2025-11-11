<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Enums\UserRole;
use App\Filament\Exports\SupplierExporter;
use App\Filament\Resources\Suppliers\SupplierResource;
use App\Filament\Resources\Suppliers\Widgets\SupplierOverview;
use App\Models\Supplier;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->hidden(function () {
                    return request()->user()->role !== UserRole::Administrator && request()->user()->role !== UserRole::Twg;
                })
                ->columnMappingColumns(2)
                ->label('Export to Excel')
                ->modifyQueryUsing(fn () => Supplier::query())
                ->fileName(fn (Export $export): string => "suppliers-{$export->getKey()}.csv")
                ->exporter(SupplierExporter::class)
                ->icon(Heroicon::ArrowDownTray),
            CreateAction::make()
                ->label('Add new Supplier')
                ->icon(Heroicon::OutlinedPlus)
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SupplierOverview::class,
        ];
    }
}
