<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Enums\ProcType;
use App\Models\LobCategory;
use App\Models\LobSubcategory;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Business Information')
                    ->description('Please provide accurate and up-to-date information about the business.')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        TextInput::make('business_name')
                            ->label('Business Name')
                            ->placeholder('e.g., ABC Enterprises')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('owner_name')
                            ->label('Name of Owner')
                            ->required()
                            ->placeholder('e.g., Juan Dela Cruz'),
                        TextInput::make('email')
                            ->email()
                            ->label('Email Address')
                            ->placeholder('e.g., abc-enterprises@example.com')
                            ->belowContent('This email address will be used to contact the business for any important inquiries and updates.')
                            ->required()
                            ->maxLength(500),
                        TextInput::make('website')
                            ->prefix('https://')
                            ->label('Website')
                            ->placeholder('e.g., www.abc-enterprises.com')
                            ->maxLength(500),
                        TextInput::make('mobile_number')
                            ->label('Mobile Number')
                            ->prefix('+63')
                            ->mask('999-999-9999')
                            ->placeholder('912-345-6789')
                            ->required()
                            ->belowContent('This number will be used to contact the business for any important inquiries and updates.')
                            ->maxLength(255),
                        TextInput::make('landline_number')
                            ->label('Landline Number')
                            ->placeholder('e.g., (082) 123-4567')
                            ->maxLength(255),
                        Select::make('supplier_type')
                            ->native(false)
                            ->searchable()
                            ->options(ProcType::class)
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Line of Business')
                    ->description('Please provide the line of business details.')
                    ->icon(Heroicon::Briefcase)
                    ->hiddenOn('create')
                    ->schema([
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
                                TableColumn::make('Category'),
                                TableColumn::make('Subcategory'),
                            ])
                            ->schema([
                                Select::make('lob_category_id')
                                    ->label('Category')
                                    ->options(fn () => LobCategory::pluck('title', 'id')->toArray())
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
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
