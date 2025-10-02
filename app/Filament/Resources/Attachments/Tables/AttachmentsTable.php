<?php

namespace App\Filament\Resources\Attachments\Tables;

use App\Enums\Status as EnumsStatus;
use App\Filament\Resources\Attachments\Actions\UpdateStatus;
use App\Helpers\DateColor;
use App\Livewire\StatusView;
use App\Models\Attachment;
use App\Models\Status;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Livewire;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;
use Illuminate\Support\HtmlString;

class AttachmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading(function ($livewire) {
                if ($livewire->activeTab === "all") {
                    return 'All Supplier Attachments';
                } else {
                    return EnumsStatus::from($livewire->activeTab)->getLabel() . ' Supplier Attachments';
                }
            })
            ->description('This table provides a comprehensive list of attachments related to suppliers, including their validity dates and file sizes.')
            ->deferLoading()
            ->striped()
            ->recordUrl(null)
            ->columns([
                TextColumn::make('document.title')
                    ->searchable(),
                TextColumn::make('supplier.business_name')
                    ->label('Supplier')
                    ->searchable(),
                TextColumn::make('validity_date')
                    ->date('F d, Y')
                    ->badge()
                    ->color(function ($state) {
                        return DateColor::getColor($state);
                    })
                    ->label('Valid Until'),
                TextColumn::make('status')
                    ->state(function ($record) {
                        return Status::where('statusable_type', Attachment::class)
                            ->where('statusable_id', $record->id)
                            ->latest()
                            ->first();
                    })
                    ->html()
                    ->formatStateUsing(function ($state): HtmlString {
                        $formatted_datetime = $state?->status_date ? Carbon::parse($state->status_date)->format('F d, Y h:i A') : '';
                        return new HtmlString($state ? $state->status->getLabel() . '<br>' . $formatted_datetime : null);
                    })
                    ->badge()
                    ->color(fn ($state) => $state->status->getColor()),
                TextColumn::make('file_size')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 2) . ' Kb' : null),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'visibility: hidden;'])
                    ->label('View Details')
                    ->icon(Heroicon::Eye)
                    ->modalWidth(Width::ExtraLarge)
                    ->modalHeading(fn ($record) => $record->document->title . ' - ' . $record->supplier->business_name)
                    ->modalDescription('View the details of this attachment, including the validity date and status history.')
                    ->modalCancelAction(fn (Action $action) => $action->label('Close')->icon(Heroicon::XMark))
                    ->slideOver()
                    ->schema([
                        Livewire::make(StatusView::class, function ($record) {
                                return [
                                    'statuses' => $record->statuses,
                                ];
                            })
                            ->extraAttributes(['class' => 'ms-5'])
                    ]),
                ActionGroup::make([
                    ActionGroup::make([
                        MediaAction::make('file_path')
                            ->icon(Heroicon::OutlinedPaperClip)
                            ->label('View Document')
                            ->modalWidth(Width::SevenExtraLarge)
                            ->mediaType('pdf')
                            ->media(fn (Attachment $record) => '/' . $record->file_path),
                    ])->dropdown(false),
                    ActionGroup::make([
                        Action::make('status')
                            ->icon(Heroicon::PencilSquare)
                            ->label('Update Status')
                            ->modal()
                            ->slideOver()
                            ->fillForm(function ($record) {
                                return [
                                    'status' => Status::where('statusable_type', Attachment::class)
                                        ->where('statusable_id', $record->id)
                                        ->latest()->first()?->status,
                                ];
                            })
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('Status')
                                    ->options(EnumsStatus::class)
                                    ->inline()
                                    ->required(),
                                RichEditor::make('remarks')
                                    ->label('Remarks')
                                    ->required()
                                    ->toolbarButtons([
                                        'italic',
                                        'underline',
                                        'strike',
                                    ])
                            ])
                            ->action(fn (Attachment $record, array $data) => UpdateStatus::save($record, $data))
                            ->after(fn ($livewire) => $livewire->dispatch('refreshAttachmentResource'))
                            ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedPlusCircle))
                            ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                            ->modalHeading(fn ($record) => $record->document->title . ' - ' . $record->supplier->business_name)
                            ->modalDescription('Update the status of this attachment. You can provide remarks or additional information regarding the status change.')
                            ->modalWidth(Width::ExtraLarge),
                    ])->dropdown(false)
                ])
                ->color('primary')
                ->button()
                ->size(Size::Small)
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
