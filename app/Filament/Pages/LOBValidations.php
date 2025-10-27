<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use App\Enums\UserRole;
use App\Livewire\StatusView;
use App\Models\Supplier;
use App\Settings\SiteSettings;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use App\Enums\Status as EnumsStatus;
use App\Models\Attachment;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Forms\Components\ViewField;
use UnitEnum;

class LOBValidations extends Page implements HasTable, HasActions
{
    use InteractsWithTable;
    use InteractsWithActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string $settings = SiteSettings::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Suppliers;

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'LOB Validations';

    protected string $view = 'filament.pages.l-o-b-validations';

    protected $listeners = ['refreshLOBpage' => '$refresh'];

    public static function canAccess(): bool
    {
        return request()->user()->role === UserRole::Administrator;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Supplier::query())
            ->heading('Line of Business Validations')
            ->description('This table displays all suppliers and their respective line of business (LOB) validation statuses. You can review the details and take necessary actions for each supplier\'s LOB submissions.')
            ->deferLoading()
            ->striped()
            ->columns([
                TextColumn::make('index')
                    ->width('1%')
                    ->rowIndex()
                    ->label('#'),
                TextColumn::make('business_name')
                    ->label('Business Name')
                    ->getStateUsing(fn ($record, $table): array => [
                        TextColumn::make('business_name')->record($record)->weight(FontWeight::Bold)->size(TextSize::Large)->color('primary')->table($table)->inline(),
                        TextColumn::make('user.email')->record($record)->prefix('System user: ')
                            ->formatStateUsing(fn ($record) => $record->user->name . ' (' . $record->user->email . ')')
                            ->icon(Heroicon::User)->table($table)->inline(),
                    ])
                    ->listWithLineBreaks()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->state(function ($record) {
                        return $record->lob_statuses()->latest()->first();
                    })
                    ->html()
                    ->formatStateUsing(function ($state): HtmlString {
                        $formatted_datetime = $state?->status_date ? Carbon::parse($state->status_date)->format('F d, Y h:i A') : '';
                        return new HtmlString($state ? '<div class="flex flex-col"><span class="font-bold">' . $state->status->getLabel() . '</span><span>' . $formatted_datetime . '</span></div>' : null);
                    })
                    ->badge()
                    ->color(fn ($state) => $state->status->getColor()),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View Status')
                    ->icon(Heroicon::Eye)
                    ->modalWidth(Width::ExtraLarge)
                    ->modalHeading(fn ($record) => $record->business_name)
                    ->modalDescription('View the details of this attachment, including the validity date and status history.')
                    ->modalCancelAction(fn (Action $action) => $action->label('Close')->icon(Heroicon::XMark))
                    ->slideOver()
                    ->modal()
                    ->schema([
                        Livewire::make(StatusView::class, function ($record) {
                                return [
                                    'statuses' => $record->lob_statuses,
                                ];
                            })
                            ->extraAttributes(['class' => 'ms-5'])
                    ]),
                Action::make('status')
                    ->icon(Heroicon::PencilSquare)
                    ->label('Update Status')
                    ->modal()
                    ->slideOver()
                    ->fillForm(function ($record) {
                        return [
                            'status' => $record->lob_statuses()->latest()->first()->status,
                        ];
                    })
                    ->schema([
                        ViewField::make('lob_listings')
                            ->view('filament.forms.components.supplier-lob-listings', function ($record) {
                                // create array of titles
                                $array = $record->supplierLobs()
                                    ->get()
                                    ->groupBy('lob_category_id')
                                    ->map(function ($items, $categoryId) {
                                        return [
                                            'lobCategory' => $items->first()->lobCategory,
                                            'lob_subcategories_list' => $items->pluck('lobSubcategory.title')->filter(),
                                        ];
                                    })
                                    ->values()
                                    ->toArray();

                                $lob_reference_document = app(SiteSettings::class)->lob_reference_document;
                                if ($lob_reference_document) {
                                    $document = Attachment::where('supplier_id', $record->id)
                                        ->where('document_id', $lob_reference_document)
                                        ->first();
                                }

                                return [
                                    'lob_listings' => $array,
                                    'reference_document' => $document ? ($document->is_validated ? $document : null) : null,
                                ];
                            })
                            ->label('Instructions'),
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
                    ->action(function ($record, array $data) {
                        $record->lob_statuses()->create([
                            'user_id' => request()->user()->id,
                            'status' => $data['status'],
                            'status_date' => now(),
                            'remarks' => $data['remarks'],
                        ]);

                        Notification::make()
                            ->title('Success')
                            ->body('Attachment status updated successfully.')
                            ->success()
                            ->send();
                    })
                    ->after(fn ($livewire) => $livewire->dispatch('refreshLOBpage'))
                    ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedPlusCircle))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->modalHeading(fn ($record) => $record->business_name)
                    ->modalDescription('Update the status of supplier\'s line of business. You can provide remarks or additional information regarding the status change.')
                    ->modalWidth(Width::ExtraLarge),
            ])
            ->toolbarActions([
                //
            ]);
    }

}
