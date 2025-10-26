<?php

namespace App\Helpers;

use App\Helpers\Validations\Models\AddressValidation;
use App\Helpers\Validations\Models\DocumentValidation;
use App\Models\Address;
use App\Models\Document;
use App\Models\Supplier;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

class SupplierStatus
{
    private AddressValidation $addressValidation;
    private DocumentValidation $documentValidation;

    private bool $isAddressValid = false;
    private bool $isDocumentValid = false;

    public function __construct(Supplier $supplier)
    {
        $addressValidation = new AddressValidation($supplier);
        $documentValidation = new DocumentValidation($supplier);

        $this->isAddressValid = $addressValidation->isValid();
        $this->isDocumentValid = $documentValidation->isValid();

        $this->addressValidation = $addressValidation;
        $this->documentValidation = $documentValidation;
    }

    public function getLabels(): array
    {
        return [
            [
                'label' => $this->documentValidation->getLabel(),
                'color' => $this->documentValidation->getColor(),
            ],
            [
                'label' => $this->addressValidation->getLabel(),
                'color' => $this->addressValidation->getColor(),
            ],
        ];
    }

    public function isFullyValidated(): bool
    {
        return $this->isAddressValid
            && $this->isDocumentValid;
    }
}
