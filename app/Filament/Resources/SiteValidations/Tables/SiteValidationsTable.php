<?php

namespace App\Filament\Resources\SiteValidations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                //
            ])
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
                        $latestRecord = $record->supplier->site_validations()->latest('created_at')->first();
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
