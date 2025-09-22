<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use App\Models\Document;
use Asmit\FilamentUpload\Enums\PdfViewFit;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;
use Hugomyb\FilamentMediaAction\Facades\FilamentMediaAction;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Supplier Documents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('document.title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('document.title')
            ->description('This table displays the documents of the selected supplier. You may also add or edit documents as needed.')
            ->emptyStateHeading('No Documents Found')
            ->emptyStateDescription('Get started by creating a new document.')
            ->modifyQueryUsing(function ($query, $record) {
                // display all documents instead of attachments
                $procurement_type = $this->getOwnerRecord()->supplier_type->value;
                return Document::where('procurement_type', 'LIKE', '%' . $procurement_type . '%');
            })
            ->columns([
                TextColumn::make('title')
                    ->color('primary')
                    ->weight('bold')
                    ->tooltip(fn (Document $record): string => $record->description)
                    ->description(fn (Document $record): string => substr($record->description, 0, 70) . (strlen($record->description) > 70 ? '...' : ''))
                    ->searchable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                // EditAction::make(),
                // DissociateAction::make(),
                // DeleteAction::make(),
                MediaAction::make('file_path')
                    ->hidden(fn (Document $record) => !$this->getOwnerRecord()->attachments()->where('document_id', $record->id)->exists())
                    ->icon(Heroicon::OutlinedPaperClip)
                    ->color('info')
                    ->label('View Document')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->mediaType('pdf')
                    ->media(function (Document $record) {
                        $attachment = $this->getOwnerRecord()->attachments()->where('document_id', $record->id)->first();
                        return $attachment ? '/' . $attachment->file_path : null;
                    }),
                Action::make('attach')
                    ->label('Attach/Replce Document')
                    ->modal()
                    ->modalWidth(Width::SevenExtraLarge)
                    ->modalHeading(fn (Document $record): string => "Attach " . $record->title)
                    ->modalDescription('Select a scanned PDF document')
                    ->modalSubmitAction(fn (Action $action) => $action->label('Save Document')->icon(Heroicon::OutlinedPlusCircle))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->modalIcon(Heroicon::Link)
                    ->icon(Heroicon::Link)
                    ->modalAlignment(Alignment::Center)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                AdvancedFileUpload::make('file_path')
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                            ->prepend((string) now()->format('YmdHis') . '_')
                                    )
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        if ($state instanceof TemporaryUploadedFile) {
                                            $set('file_size', $state->getSize());
                                        }
                                    })
                                    ->columnSpan(2)
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('attachments')
                                    ->disk('public')
                                    ->hiddenLabel()
                                    ->openable()
                                    ->downloadable()
                                    ->moveFiles()
                                    ->maxSize(5120) // 5MB
                                    ->pdfDisplayPage(1)
                                    ->pdfToolbar(true)
                                    ->pdfZoomLevel(150)
                                    ->pdfPreviewHeight(600)
                                    ->pdfFitType(PdfViewFit::FITV)
                                    ->pdfNavPanes(true),
                                DatePicker::make('validity_date')
                                    ->label('This document is valid until')
                                    ->placeholder('Select validity date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('F d, Y')
                                    ->firstDayOfWeek(1),
                                Hidden::make('file_size')
                            ])
                    ])
                    ->action(function (Document $record, $data) {
                        $supplier = $this->getOwnerRecord();
                        $attachment = $supplier->attachments()->where('document_id', $record->id)->first();
                        if (!$attachment) {
                            // Create new pivot record
                            $supplier->attachments()->create([
                                'document_id' => $record->id,
                                'file_path' => $data['file_path'],
                                'validity_date' => $data['validity_date'],
                                'file_size' => $data['file_size'],
                            ]);
                        } else {
                            // Remove existing file from storage
                            Storage::disk('public')->delete($attachment->file_path);
                            // Update existing record
                            $attachment->update([
                                'file_path' => $data['file_path'],
                                'validity_date' => $data['validity_date'],
                                'file_size' => $data['file_size'],
                            ]);
                        }

                        Notification::make()
                            ->title('Document attached successfully')
                            ->success()
                            ->send();
                    })
                    ->color('primary'),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DissociateBulkAction::make(),
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
