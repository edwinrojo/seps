<?php

namespace App\Helpers\Validations\Models;

use App\Helpers\Validations\Interfaces\Validator;
use App\Models\Supplier;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

class AddressValidation implements Validator, HasLabel, HasColor
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
        if ($this->supplier->addresses->isEmpty()) {
            $this->label = 'No addresses uploaded';
            $this->color = 'warning';
            return false;
        }

        $is_site_validated = true;

        foreach ($this->supplier->addresses as $address) {
            if (! $address->is_validated) {
                $is_site_validated = false;
                break;
            }
        }

        $this->label = $is_site_validated ? 'Address Validated' : 'Pending address validation';
        $this->color = $is_site_validated ? 'success' : 'warning';
        return $is_site_validated;
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
