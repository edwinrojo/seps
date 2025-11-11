<?php

namespace App\Filament\Resources\SiteValidations\Tables;

use App\Models\ValidationPurpose;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SiteValidationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Site Validations')
            ->description('List of all site validations conducted by the Technical Working Group (TWG). Only the latest site validation for each supplier can be edited.')
            ->modifyQueryUsing(fn ($query) => $query->orderBy('validation_date', 'desc'))
            ->columns([
                TextColumn::make('supplier.business_name')
                    ->color('primary')
                    ->weight('bold')
                    ->description(function ($record) {
                        return $record->address->full_address;
                    })
                    ->label('Supplier')
                    ->searchable(),
                TextColumn::make('twg.user.name')
                    ->label('TWG Member')
                    ->searchable(),
                TextColumn::make('validation_purposes.purpose')
                    ->listWithLineBreaks()
                    ->bulleted()
                    // ->badge()
                    ->label('Purpose')
                    ->searchable(),
                TextColumn::make('validation_date')
                    ->badge()
                    ->dateTime('F d, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('validation_purpose')
                    ->label('Purpose')
                    ->indicator('Purpose')
                    ->schema([
                        Select::make('validation_purposes')
                            ->options(ValidationPurpose::pluck('purpose', 'id')->toArray())
                            ->multiple()
                            ->native(false),
                        Select::make('twg_id')
                            ->label('TWG Member')
                            ->options(function () {
                                return \App\Models\User::where('role', 'twg')->pluck('name', 'id')->toArray();
                            })
                            ->native(false)
                            ->searchable()
                            ->placeholder('Select TWG Member'),
                        DatePicker::make('month_and_year')
                            ->native()
                            ->extraInputAttributes(['type' => 'month'])
                            ->closeOnDateSelection()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['validation_purposes'],
                                function (Builder $query, $data): Builder {
                                    // return site validations that contains any of the selected purposes
                                    return $query->whereHas('validation_purposes', function ($q) use ($data) {
                                        $q->whereIn('id', $data);
                                    });
                                }
                            )
                            ->when(
                                $data['twg_id'],
                                function (Builder $query, $data): Builder {
                                    return $query->where('twg_id', $data);
                                }
                            )
                            ->when(
                                $data['month_and_year'],
                                function (Builder $query, $data): Builder {
                                    $date = \Carbon\Carbon::createFromFormat('Y-m', $data);
                                    return $query->whereYear('validation_date', $date->year)
                                        ->whereMonth('validation_date', $date->month);
                                }
                            );
                    }),
            ], layout: FiltersLayout::AfterContent)
            ->recordUrl(null)
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Site Validation Details')
                    ->modalDescription('View detailed information about this site validation.')
                    ->modalCancelAction(fn ($action) => $action->label('Close')->icon(Heroicon::OutlinedXMark))
                    ->extraAttributes(['style' => 'visibility: hidden;']),
                EditAction::make()
                    ->hidden(function ($record) {
                        // hide if record is not latest
                        $latestRecord = $record->supplier->site_validations()->latest('validation_date')->first();
                        return $record->id !== $latestRecord->id;
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
