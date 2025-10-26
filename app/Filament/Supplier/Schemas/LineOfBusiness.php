<?php

namespace App\Filament\Supplier\Schemas;

use App\Enums\ProcType;
use App\Models\LobCategory;
use App\Models\LobSubcategory;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

class LineOfBusiness
{
    public static function getSchema(): array
    {
        return [
            Repeater::make('supplierLobs')
                ->hiddenLabel()
                ->columns(2)
                ->grid(2)
                ->addActionAlignment(Alignment::Start)
                ->addActionLabel('Add Line of Business')
                ->addAction(fn ($action) => $action->icon(Heroicon::OutlinedPlusCircle))
                ->columnSpanFull()
                ->deleteAction(
                    fn (Action $action) => $action->requiresConfirmation(),
                )
                ->table([
                    TableColumn::make('Category')->markAsRequired(),
                    TableColumn::make('Subcategory')->markAsRequired(),
                ])
                ->schema([
                    Select::make('lob_category_id')
                        ->label('Category')
                        ->options(function () {
                            $lobCategories = LobCategory::all();
                            // add description to next line via HtmlString
                            return $lobCategories->pluck('title', 'id')->mapWithKeys(function ($title, $id) use ($lobCategories) {
                                $description = $lobCategories->where('id', $id)->first()->description;
                                return [$id => '<b>'.$title.'</b>' . ($description ? "<span style='display: block;' class='text-sm text-gray-500'>$description</span>" : '')];
                            });
                        })
                        ->allowHtml()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('lob_subcategory_id', null))
                        ->searchable()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->placeholder('Select Category'),
                    Select::make('lob_subcategory_id')
                        ->label('Subcategory')
                        ->multiple()
                        ->required()
                        ->searchable()
                        ->hidden(function (callable $get) {
                            $categoryId = $get('lob_category_id');
                            $subcategories = LobSubcategory::where('lob_category_id', $categoryId)
                                ->pluck('title', 'id')
                                ->toArray();
                            return $subcategories === [] || !$categoryId;
                        })
                        ->options(function (callable $get) {
                            $categoryId = $get('lob_category_id');
                            if (!$categoryId) {
                                return [];
                            }
                            return LobSubcategory::where('lob_category_id', $categoryId)
                                ->pluck('title', 'id')
                                ->toArray();
                        })
                        ->placeholder('Select Subcategory'),
                ]),
        ];
    }
}
