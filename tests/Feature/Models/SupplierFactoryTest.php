<?php

use App\Enums\ProcType;
use App\Models\Supplier;
use Database\Factories\SupplierFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a supplier using the supplier factory', function () {
    $supplier = SupplierFactory::new()->create();

    expect($supplier)->toBeInstanceOf(Supplier::class)
        ->and($supplier->business_name)->not->toBeEmpty()
        ->and($supplier->email)->not->toBeEmpty()
        ->and($supplier->supplier_type)->toBeInstanceOf(ProcType::class);
});
