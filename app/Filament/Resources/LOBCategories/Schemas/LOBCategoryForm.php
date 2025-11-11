<?php

namespace App\Filament\Resources\LOBCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class LOBCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Line of Business Category Details')
                    ->description('Provide the necessary details for the Line of Business Category.')
                    ->icon(Heroicon::InformationCircle)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Category Title')
                            ->placeholder('e.g., Construction, IT Services')
                            ->unique()
                            ->validationMessages([
                                "unique" => "This Line of Business Category already exists."
                            ])
                            ->helperText('Enter the title of the Line of Business Category.')
                            ->required(),
                        Textarea::make('description')
                            ->label('Category Description')
                            ->placeholder('Provide a brief description of the category.')
                            ->helperText('Describe the Line of Business Category in detail.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
