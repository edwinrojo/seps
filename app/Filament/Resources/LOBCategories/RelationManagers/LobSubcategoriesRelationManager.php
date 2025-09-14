<?php

namespace App\Filament\Resources\LOBCategories\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LobSubcategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'lob_subcategories';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->columnSpanFull()
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    protected function getTableHeading(): string
    {
        return 'Line of Business Subcategories';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->description('Manage the subcategories associated with this line of business category.')
            ->columns([
                TextColumn::make('title')
                    ->columnSpanFull()
                    ->searchable(),
                TextColumn::make('description')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalSubmitAction(fn (Action $action) => $action->label('Create Subcategory')->icon(Heroicon::OutlinedPlusCircle))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->createAnother(false)
                    ->modalHeading('Create new subcategory')
                    ->modalDescription('Fill out the form below to create a new subcategory.')
                    ->icon(Heroicon::OutlinedPlusCircle)
                    ->label('New LOB Subcategory')
                    ->modalWidth(Width::Medium),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                // DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
