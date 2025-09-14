<?php

namespace App\Filament\Resources\LOBCategories\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LOBCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No Line of Business Categories Found')
            ->emptyStateDescription('Get started by creating a new line of business category.')
            ->emptyStateIcon(Heroicon::FolderPlus)
            ->emptyStateActions([
                Action::make('create')
                    ->label('Add Line of Business')
                    ->icon(Heroicon::Plus)
                    ->url(route('filament.admin.resources.l-o-b-categories.create'))
                    ->button(),
            ])
            ->columns([
                Stack::make([
                    TextColumn::make('title')
                        ->label('Title')
                        ->sortable()
                        ->color('primary')
                        ->weight(FontWeight::Bold)
                        ->searchable(),
                    TextColumn::make('description')
                        ->label('Description')
                        // ->limit(50)
                        ->wrap()
                        ->sortable(),
                    Panel::make([
                        TextColumn::make('lob_subcategories')
                            ->label('Subcategories')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->state(fn ($record) => $record->lob_subcategories->pluck('title')->toArray()),
                    ])
                    ->visible(fn ($record) => $record->lob_subcategories->isNotEmpty())
                    ->collapsible()
                    ->collapsed(false),
                ])
            ])
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'lg' => 2,
                'xl' => 2,
                '2xl' => 2,
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->button()
                    ->color('primary')
                    ->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
