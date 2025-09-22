<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Enums\ProcType;
use App\Models\DocumentType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Document Form')
                    ->description('Please provide the necessary information for the document.')
                    ->icon(Heroicon::DocumentText)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Document Title')
                            ->helperText('Enter the title of the document. E.g., Business Permit, DTI Registration')
                            ->placeholder('e.g., Business Permit, DTI Registration')
                            ->unique()
                            ->validationMessages([
                                "unique" => "This document was already created."
                            ])
                            ->required(),
                        Select::make('document_type_id')
                            ->label('Document Type')
                            ->options(function () {
                                $documentTypes = \App\Models\DocumentType::all();
                                // add description to next line via HtmlString
                                return $documentTypes->pluck('title', 'id')->mapWithKeys(function ($title, $id) use ($documentTypes) {
                                    $description = $documentTypes->where('id', $id)->first()->description;
                                    return [$id => '<b>'.$title.'</b>' . ($description ? "<span style='display: block;' class='text-sm text-gray-500'>$description</span>" : '')];
                                });
                            })
                            ->helperText('Select or create a document type. E.g., Legal Document, Financial Document')
                            ->createOptionForm([
                                TextInput::make('title')
                                    ->label('Document Type Title')
                                    ->placeholder('e.g., Legal Document, Financial Document')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->placeholder('Provide a brief description of the document type.')
                                    ->label('Document Type Description')
                            ])
                            ->createOptionUsing(function (array $data) {
                                $newDocumentType = DocumentType::create($data);

                                Notification::make()
                                    ->title('Document Type Created')
                                    ->body("The document type '{$newDocumentType->title}' has been created successfully.")
                                    ->success()
                                    ->send();

                                return $newDocumentType->id;
                            })
                            ->allowHtml()
                            ->searchable()
                            ->required(),
                        Textarea::make('description')
                            ->placeholder('Provide a brief description of the document.'),
                        Select::make('procurement_type')
                            ->options(ProcType::class)
                            ->searchable()
                            ->multiple()
                            ->required(),
                        Toggle::make('is_required')
                            ->helperText('Indicates if this document is mandatory for submission.')
                            ->required(),
                    ])
            ]);
    }
}
