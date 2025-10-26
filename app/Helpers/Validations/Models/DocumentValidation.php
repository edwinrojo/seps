<?php

namespace App\Helpers\Validations\Models;

use App\Helpers\Validations\Interfaces\Validator;
use App\Models\Document;
use App\Models\Supplier;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

class DocumentValidation implements Validator, HasLabel, HasColor
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
        if ($this->supplier->attachments->isEmpty()) {
            $this->label = 'No documents uploaded';
            $this->color = 'warning';
            return false;
        }

        $required_documents = Document::where('procurement_type', 'LIKE', '%' . $this->supplier->supplier_type->value . '%')
            ->where('is_required', true)
            ->count();
        if ($this->supplier->attachments->count() < $required_documents) {
            $this->label = 'Other required documents are missing';
            $this->color = 'warning';
            return false;
        }

        $is_document_validated = true;

        foreach ($this->supplier->attachments as $attachment) {
            if (! $attachment->is_validated) {
                $is_document_validated = false;
                break;
            }
        }

        $this->label = $is_document_validated ? 'Documents Validated' : 'Non-compliant as to document requirements';
        $this->color = $is_document_validated ? 'success' : 'warning';
        return $is_document_validated;
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
