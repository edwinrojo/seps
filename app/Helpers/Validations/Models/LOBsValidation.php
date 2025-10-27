<?php

namespace App\Helpers\Validations\Models;

use App\Helpers\Validations\Interfaces\Validator;
use App\Models\Supplier;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

class LOBsValidation implements Validator, HasLabel, HasColor
{
    private Supplier $supplier;
    private string $label;
    private string $color;

    public function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }

    public function isValid(): bool
    {
        if ($this->supplier->supplierLobs->isEmpty()) {
            $this->label = 'No Lines of Business uploaded';
            $this->color = 'warning';
            return false;
        }

        $is_lobs_validated = $this->supplier->lob_statuses()->latest()->first()?->status === \App\Enums\Status::Validated ?? false;

        $this->label = $is_lobs_validated ? 'Lines of Business Validated' : 'Pending Lines of Business validation';
        $this->color = $is_lobs_validated ? 'success' : 'warning';
        return $is_lobs_validated;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
