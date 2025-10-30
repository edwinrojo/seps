<?php

namespace App\Filament\Resources\Suppliers\Tables;

use App\Enums\Status;
use App\Filament\GlobalActions\SecureDeleteAction;
use App\Filament\Resources\Suppliers\Schemas\AssignForm;
use App\Helpers\SupplierStatus;
use App\Models\Supplier;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Official Supplier Registry')
            ->description('This table provides a comprehensive list of accredited suppliers, including their company details, contact information, and nature of goods or services offered.')
            ->deferLoading()
            ->striped()
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label('#'),
                TextColumn::make('business_name')
                    ->label('Business Name')
                    ->getStateUsing(fn ($record, $table): array => [
                        TextColumn::make('business_name')->record($record)->weight(FontWeight::Bold)->size(TextSize::Large)->color('primary')->table($table)->inline(),
                        TextColumn::make('user.email')->record($record)->prefix('System user: ')
                            ->formatStateUsing(fn ($record) => $record->user->name . ' (' . $record->user->email . ')')
                            ->icon(Heroicon::User)->table($table)->inline(),
                        TextColumn::make('website')->record($record)->icon(Heroicon::GlobeAlt)->table($table)->inline(),
                    ])
                    ->listWithLineBreaks()
                    ->sortable()
                    ->searchable(),
                // TextColumn::make('email')
                //     ->label('Email Address')
                //     ->getStateUsing(fn ($record, $table): array => [
                //         TextColumn::make('email')->record($record)->icon(Heroicon::AtSymbol)->table($table)->inline(),
                //         TextColumn::make('website')->record($record)->icon(Heroicon::GlobeAlt)->table($table)->inline(),
                //     ])
                //     ->listWithLineBreaks()
                //     ->sortable()
                //     ->searchable(),
                TextColumn::make('mobile_number')
                    ->label('Contact Information')
                    ->getStateUsing(fn ($record, $table): array => [
                        TextColumn::make('email')->record($record)->icon(Heroicon::AtSymbol)->table($table)->inline(),
                        TextColumn::make('mobile_number')->record($record)->prefix('+63 ')->icon(Heroicon::DevicePhoneMobile)->table($table)->inline(),
                        TextColumn::make('landline_number')->record($record)->icon(Heroicon::Phone)->table($table)->inline(),
                    ])
                    ->listWithLineBreaks(),
                TextColumn::make('status')
                    ->listWithLineBreaks()
                    ->state(function ($record) {
                        $supplierStatus = new SupplierStatus($record);
                        $labelsWithColors = [];
                        foreach ($supplierStatus->getLabels() as $item) {
                            $labelsWithColors = array_merge($labelsWithColors, [
                                "<span class='mb-1 fi-color fi-color-{$item['color']} fi-text-color-700 dark:fi-text-color-300 fi-badge fi-size-sm'>
                                    <div class='inline-block px-2 rounded-full text-xs font-medium fi-color fi-color-{$item['color']} fi-text-color-600'>{$item['label']}</div>
                                </span>"
                            ]);
                        }
                        return $labelsWithColors;
                    })
                    ->color('success')
                    ->html()
                    ->label('Status')
            ])
            ->filters([
                TrashedFilter::make()->native(false),
                Filter::make('eligibility_status')
                    ->label('Eligibility Status')
                    ->indicator('Eligibility Status')
                    ->schema([
                        Select::make('eligibility_status')
                            ->options([
                                'eligible' => 'Eligible Suppliers',
                                'ineligible' => 'Ineligible Suppliers',
                            ])
                            ->native(false),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['eligibility_status']) {
                            return null;
                        }

                        return 'Eligibility Status: ' . ($data['eligibility_status'] === 'eligible' ? 'Eligible Suppliers' : 'Ineligible Suppliers');
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['eligibility_status'],
                                function (Builder $query, $status): Builder {
                                    $eligibleSupplierIds = collect();
                                    $ineligibleSupplierIds = collect();
                                    $query->with([
                                        'addresses.statuses',
                                        'attachments.statuses',
                                        'lob_statuses',
                                    ])->chunkById(200, function ($suppliers) use ($eligibleSupplierIds, $ineligibleSupplierIds) {
                                        foreach ($suppliers as $supplier) {
                                            $supplierStatus = new \App\Helpers\SupplierStatus($supplier);
                                            if ($supplierStatus->isFullyValidated()) {
                                                $eligibleSupplierIds->push($supplier->id);
                                            } else {
                                                $ineligibleSupplierIds->push($supplier->id);
                                            }
                                        }
                                    });

                                    if ($status === 'eligible') {
                                        return $query->whereIn('id', $eligibleSupplierIds);
                                    }
                                    return $query->whereIn('id', $ineligibleSupplierIds);
                                }
                            );
                    }),
                Filter::make('line_of_business')
                    ->label('Line of Business')
                    ->indicator('Line of Business')
                    ->schema([
                        Select::make('line_of_business')
                            ->options(function () {
                                return \App\Models\LobCategory::pluck('title', 'id')->toArray();
                            })
                            ->multiple()
                            ->native(false)
                            ->searchable(),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['line_of_business']) {
                            return null;
                        }

                        $lobCategories = \App\Models\LobCategory::findMany($data['line_of_business']);

                        return 'Line of Business: ' . ($lobCategories->isNotEmpty() ? $lobCategories->pluck('title')->implode(', ') : '');
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['line_of_business'],
                                function (Builder $query, $lobCategoryId): Builder {
                                    return $query->whereHas('supplierLobs', function (Builder $query) use ($lobCategoryId) {
                                        // multiple ids
                                        $query->whereIn('lob_category_id', $lobCategoryId);
                                    });
                                }
                            );
                    }),
            ], layout: FiltersLayout::AfterContent)
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'visibility: hidden;']),
                ActionGroup::make([
                    EditAction::make()
                        ->hiddenLabel()
                        ->color('gray')
                        ->tooltip('Edit supplier')
                        ->icon(Heroicon::PencilSquare),
                    Action::make('assign')
                        ->hiddenLabel()
                        ->color('gray')
                        ->tooltip('Assign/Edit user account')
                        ->extraAttributes(['class' => 'success-icon-color'])
                        ->icon(Heroicon::UserPlus)
                        ->modal()
                        ->modalWidth(Width::Large)
                        ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedCheck))
                        ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                        ->slideOver()
                        ->modalHeading(fn ($record) => $record->business_name)
                        ->modalDescription('Assign this supplier to an existing user account. This will link the supplier record to the selected user for business profile management and tracking.')
                        ->fillForm(fn ($record) => [
                            'user_id' => $record->user_id,
                        ])
                        ->schema(AssignForm::configure())
                        ->action(function (array $data, $record) {
                            $data['user_id'] ? $record->update(['user_id' => $data['user_id']]) : null;

                            Notification::make()
                                ->title('Supplier Assigned')
                                ->body('The supplier has been successfully assigned to the user account.')
                                ->success()
                                ->send();
                        }),
                    SecureDeleteAction::make()
                        ->hiddenLabel()
                        ->color('gray')
                        ->tooltip('Delete supplier')
                        ->extraAttributes(['class' => 'danger-icon-color'])
                        ->icon(Heroicon::Trash),
                ])->buttonGroup()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
