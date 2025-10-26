<?php

namespace App\Helpers\Validations\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface Validator
{
    public function isValid(): bool;
}
