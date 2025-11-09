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
use App\Filament\Pages\Schemas\LOBValidationsModalForm;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Concerns\HasTabs;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Filament\Notifications\Notifications;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class LOBValidations extends Page implements HasTable, HasActions, HasForms
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithForms;
    use HasTabs;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string $settings = SiteSettings::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Suppliers;

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'LOB Validations';

    protected string $view = 'filament.pages.l-o-b-validations';

    protected $listeners = ['refreshLOBpage' => '$refresh'];

    public static function canAccess(): bool
    {
        return request()->user()->role === UserRole::Administrator || request()->user()->role === UserRole::Twg;
    }

    public function mount(): void
    {
        $this->loadDefaultActiveTab();
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Records')
                ->icon(Heroicon::ListBullet)
                ->badgeColor('info')
                ->badge(Supplier::whereHas('lob_statuses', function ($query) {
                })->count()),
            'validated' => Tab::make('Validated')
                ->icon(Heroicon::ShieldCheck)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $this->getValidatedTabQuery($query))
                ->badge(fn () => Supplier::whereHas('latestStatus', function ($query) {
                    $query->where('status', EnumsStatus::Validated);
                })->count()),
            'pending' => Tab::make('Pending Review')
                ->icon(Heroicon::Clock)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $this->getPendingTabQuery($query))
                ->badge(fn () => Supplier::whereHas('latestStatus', function ($query) {
                    $query->where('status', EnumsStatus::PendingReview);
                })->count()),
            'rejected' => Tab::make('Rejected')
                ->icon(Heroicon::XCircle)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $this->getRejectedTabQuery($query))
                ->badge(fn () => Supplier::whereHas('latestStatus', function ($query) {
                    $query->where('status', EnumsStatus::Rejected);
                })->count()),
            'expired' => Tab::make('Expired')
                ->icon(Heroicon::ExclamationCircle)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $this->getExpiredTabQuery($query))
                ->badge(fn () => Supplier::whereHas('latestStatus', function ($query) {
                    $query->where('status', EnumsStatus::Expired);
                })->count()),
        ];
    }

    public function getValidatedTabQuery(Builder $query): Builder
    {
        return $query->whereHas('latestStatus', function (Builder $query1) {
            $query1->where('status', EnumsStatus::Validated);
        });
    }

    public function getPendingTabQuery(Builder $query): Builder
    {
        return $query->whereHas('latestStatus', function (Builder $query1) {
            $query1->where('status', EnumsStatus::PendingReview);
        });
    }

    public function getRejectedTabQuery(Builder $query): Builder
    {
        return $query->whereHas('latestStatus', function (Builder $query1) {
            $query1->where('status', EnumsStatus::Rejected);
        });
    }

    public function getExpiredTabQuery(Builder $query): Builder
    {
        return $query->whereHas('latestStatus', function (Builder $query1) {
            $query1->where('status', EnumsStatus::Expired);
        });
    }

    public function getTabsContentComponent(): Component
    {
        $tabs = $this->getCachedTabs();

        return Tabs::make()
            ->livewireProperty('activeTab')
            ->contained(false)
            ->tabs($tabs)
            ->extraAttributes(['style' => 'display: block;'])
            ->hidden(empty($tabs));
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(), // This method returns a component to display the tabs above a table
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(), // This is the component that renders the table that is defined in this resource
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Supplier::whereHas('lob_statuses'))
            ->modifyQueryUsing(fn ($query) => $this->modifyQueryWithActiveTab($query))
            ->heading(function ($livewire) {
                switch ($livewire->activeTab) {
                    case 'all':
                        return 'All Records';
                    case 'validated':
                        return 'Validated Supplier LOBs';
                    case 'pending':
                        return 'Pending Supplier LOBs';
                    case 'rejected':
                        return 'Rejected Supplier LOBs';
                    case 'expired':
                        return 'Expired Supplier LOBs';
                    default:
                        return 'Line of Business Validations';
                }
            })
            ->description('This table displays all suppliers and their respective line of business (LOB) validation statuses. You can review the details and take necessary actions for each supplier\'s LOB submissions.')
            ->deferLoading()
            ->striped()
            ->recordUrl(null)
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
                    ->modal(function () {
                        $lob_reference_document = app(SiteSettings::class)->lob_reference_document;
                        return $lob_reference_document !== null;
                    })
                    ->slideOver()
                    ->fillForm(function ($record) {
                        return [
                            'status' => $record->lob_statuses()->latest()->first()->status,
                        ];
                    })
                    ->schema(function () {
                        $lob_reference_document = app(SiteSettings::class)->lob_reference_document;
                        if (!$lob_reference_document) {
                            return null;
                        }
                        return LOBValidationsModalForm::configure();
                    })
                    ->action(function ($record, array $data) {
                        $lob_reference_document = app(SiteSettings::class)->lob_reference_document;
                        if (!$lob_reference_document) {
                            Notification::make()
                                ->title('Configuration Missing')
                                ->body('The LOB Reference Document is not configured in Site Settings. Please contact the system administrator.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->lob_statuses()->create([
                            'user_id' => request()->user()->id,
                            'status' => $data['status'],
                            'status_date' => now(),
                            'remarks' => $data['remarks'],
                        ]);

                        Notification::make()
                            ->title('Success')
                            ->body('LOBs status updated successfully.')
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
