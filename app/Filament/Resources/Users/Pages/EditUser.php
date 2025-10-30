<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\ProcType;
use App\Enums\UserRole;
use App\Filament\GlobalActions\SecureDeleteAction;
use App\Filament\Resources\Users\Schemas\UserWizard;
use App\Filament\Resources\Users\UserResource;
use App\Models\Office;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    use HasWizard;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SecureDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
            ForceDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
            RestoreAction::make()
                ->icon(Heroicon::OutlinedArrowUturnLeft),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Save User'),
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
            ->submitAction($this->getSubmitFormAction()->icon(Heroicon::OutlinedCheckCircle)->label('Save User'))
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // password update only when not empty
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $record->update($data);

        switch ($record->role->value) {
            case 'twg':
                $record->twg()->update([
                    'office_id' => $data['twg']['office_id'],
                    'position_title' => $data['twg']['position_title'],
                    'twg_type' => $data['twg']['twg_type'],
                ]);
                break;
            case 'end-user':
                $record->endUser()->update([
                    'office_id' => $data['endUser']['office_id'],
                    'designation' => $data['endUser']['designation'],
                ]);
                break;
            default:
                break;
        }

        return $record;
    }
}
