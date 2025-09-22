<?php

namespace App\Filament\Resources\Documents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Document Registry')
            ->description('This table contains all the documents that are being managed within the system, including their types, procurement categories, and status indicators.')
            ->deferLoading()
            ->striped()
            ->columns([
                TextColumn::make('title')
                    ->description(function ($livewire, $record) {
                        if ($livewire->activeTab === 'all') {
                            return $record->documentType->title;
                        }
                    })
                    ->color('primary')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('description')
                    ->words(10)
                    ->tooltip(fn ($state) => $state)
                    ->lineClamp(2)
                    ->wrap(),
                TextColumn::make('procurement_type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'goods' => 'success',
                        'infrastructure' => 'primary',
                        'consulting services' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                IconColumn::make('is_required')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
