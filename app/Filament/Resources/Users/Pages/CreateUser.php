<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\ProcType;
use App\Enums\UserRole;
use App\Filament\Resources\Users\Schemas\UserWizard;
use App\Filament\Resources\Users\UserResource;
use App\Models\Office;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    use HasWizard;

    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make('1234');
        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            // ...parent::getFormActions(),
            $this->getCreateFormAction()
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Create User'),
            $this->getCancelFormAction()
                ->icon(Heroicon::OutlinedXMark)
                ->label('Cancel'),
        ];
    }

    public function getWizardComponent(): Component
    {
        return Wizard::make($this->getSteps())
            ->startOnStep($this->getStartStep())
            ->cancelAction($this->getCancelFormAction()->icon(Heroicon::OutlinedXMark)->label('Cancel'))
            ->submitAction($this->getSubmitFormAction()->icon(Heroicon::OutlinedCheckCircle)->label('Create User'))
            ->nextAction(fn (Action $action) => $action->label('Next Step')->icon(Heroicon::ArrowRight))
            ->previousAction(fn (Action $action) => $action->label('Previous Step')->icon(Heroicon::ArrowLeft))
            ->alpineSubmitHandler("\$wire.{$this->getSubmitFormLivewireMethodName()}()")
            ->skippable($this->hasSkippableSteps())
            ->contained(true);
    }

    protected function getSteps(): array
    {
        return UserWizard::getSteps();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $user = parent::handleRecordCreation($data);

        switch ($user->role->value) {
            case 'twg':
                $user->twg()->create([
                    'user_id' => $user->id,
                    'office_id' => $data['twg']['office_id'],
                    'position_title' => $data['twg']['position_title'],
                    'twg_type' => $data['twg']['twg_type'],
                ]);
                break;
            case 'end-user':
                $user->endUser()->create([
                    'user_id' => $user->id,
                    'office_id' => $data['endUser']['office_id'],
                    'designation' => $data['endUser']['designation'],
                ]);
                break;
            default:
                break;
        }

        return $user;
    }
}
