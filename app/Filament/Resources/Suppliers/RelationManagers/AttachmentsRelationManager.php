<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use App\Enums\Status;
use App\Filament\Resources\Suppliers\Actions\AttachDocument;
use App\Filament\Resources\Suppliers\Schemas\SupplierDocumentsColumns;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\Supplier;
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
            ->columns(SupplierDocumentsColumns::get($this->getOwnerRecord()))
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
                    ->label('Attach/Replace Document')
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
                                    ->required()
                                    ->columnSpan(2)
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('attachments')
                                    ->disk('public')
                                    ->hiddenLabel()
                                    ->openable()
                                    ->downloadable()
                                    ->moveFiles()
                                    ->minSize(1)
                                    ->maxSize(5120)
                                    ->validationMessages([
                                        'max' => 'File size must not exceed 5MB.'
                                    ])
                                    ->pdfDisplayPage(1)
                                    ->pdfToolbar(true)
                                    ->pdfZoomLevel(150)
                                    ->pdfPreviewHeight(600)
                                    ->pdfFitType(PdfViewFit::FITV)
                                    ->pdfNavPanes(true),
                                DatePicker::make('validity_date')
                                    ->label('Validity Date')
                                    ->belowLabel('Leave blank if not applicable')
                                    ->placeholder('Select validity date')
                                    ->native(false)
                                    ->displayFormat('F d, Y')
                                    ->firstDayOfWeek(1),
                                Hidden::make('file_size')
                            ])
                    ])
                    ->action(fn (Document $record, $data) => AttachDocument::handle($this->getOwnerRecord(), $record, $data))
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
