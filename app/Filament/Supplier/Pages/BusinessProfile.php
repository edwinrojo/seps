<?php

namespace App\Filament\Supplier\Pages;

use App\Enums\ProcType;
use App\Filament\GlobalActions\SiteImageAction;
use App\Filament\Resources\Suppliers\Schemas\SupplierInfolist;
use App\Filament\Supplier\Schemas\BusinessInformation;
use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;
use Filament\Support\Enums\Alignment;
use App\Models\Supplier;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\LobCategory;
use App\Models\LobSubcategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Schemas\Components\Grid;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BusinessProfile extends Page
{
    protected string $view = 'filament.supplier.pages.business-profile';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    protected static ?string $title = 'My Business Profile';

    protected ?string $subheading = 'Manage your business profile information and upload necessary documents to verify your business\' identity and eligibility.';

    protected $listeners = ['refreshInfolist' => '$refresh'];

    public ?Supplier $record = null;

    public $defaultAction = 'onboarding';

    public function mount(): void
    {
        $this->record = request()->user()?->supplier;
    }

    public function infolist(Schema $schema): Schema
    {
        return SupplierInfolist::configure($schema)->record($this->record);
    }

    public function onboardingAction(): Action
    {
        return Action::make('onboarding')
            ->modal()
            ->modalHeading('Setup Business Information')
            ->modalDescription('Setup your business information in the form below.')
            ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedPlusCircle))
            ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
            ->modalIcon(Heroicon::PencilSquare)
            ->icon(Heroicon::PencilSquare)
            ->modalWidth(Width::FiveExtraLarge)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalAutofocus(false)
            ->schema([
                Grid::make(2)
                    ->schema(BusinessInformation::getSchema())
            ])
            ->after(fn ($livewire) => $livewire->dispatch('refreshInfolist'))
            ->action(function (array $data): void {
                $user = request()->user();
                $data['user_id'] = $user->id;
                Supplier::create($data);

                Notification::make()
                    ->title('Saved successfully')
                    ->success()
                    ->body('Record have been saved.')
                    ->send();
            })
            ->visible(fn (): bool => $this->record === null);
    }
}
