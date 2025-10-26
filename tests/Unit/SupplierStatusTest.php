<?php

use App\Enums\ProcType;
use App\Helpers\SupplierStatus;
use App\Models\Address;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Create a supplier for testing
    $this->supplier = Supplier::factory()->create([
        'supplier_type' => ProcType::GOODS,
    ]);
});

test('constructor initializes validation states correctly', function () {
    // Create a supplier with no addresses and no attachments
    $supplier = Supplier::factory()->create([
        'supplier_type' => ProcType::GOODS,
    ]);

    $supplierStatus = new SupplierStatus($supplier);

    // Both validations should be false for empty collections
    expect($supplierStatus->isFullyValidated())->toBeFalse();
});

test('isFullyValidated returns true when both address and document validations pass', function () {
    // Create required documents
    Document::factory()->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create validated address
    Address::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    // Create validated attachment
    Attachment::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeTrue();
});

test('isFullyValidated returns false when address validation fails', function () {
    // Create required documents
    Document::factory()->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create unvalidated address
    Address::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => false,
    ]);

    // Create validated attachment
    Attachment::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();
});

test('isFullyValidated returns false when document validation fails', function () {
    // Create required documents
    Document::factory()->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create validated address
    Address::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    // Create unvalidated attachment
    Attachment::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => false,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();
});

test('isFullyValidated returns false when no addresses exist', function () {
    // Create required documents
    Document::factory()->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create validated attachment
    Attachment::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();
});

test('isFullyValidated returns false when no attachments exist', function () {
    // Create required documents
    Document::factory()->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create validated address
    Address::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();
});

test('getLabels returns correct array structure with document and address labels', function () {
    // Create required documents
    Document::factory()->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create validated address
    Address::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    // Create validated attachment
    Attachment::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);
    $labels = $supplierStatus->getLabels();

    expect($labels)->toBeArray()
        ->and($labels)->toHaveCount(2)
        ->and($labels[0])->toHaveKeys(['label', 'color'])
        ->and($labels[1])->toHaveKeys(['label', 'color'])
        ->and($labels[0]['label'])->toBe('Documents Validated')
        ->and($labels[0]['color'])->toBe('success')
        ->and($labels[1]['label'])->toBe('Address Validated')
        ->and($labels[1]['color'])->toBe('success');
});

test('getLabels returns warning labels when validations fail', function () {
    // Create required documents
    Document::factory()->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create unvalidated address
    Address::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => false,
    ]);

    // Create unvalidated attachment
    Attachment::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => false,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);
    $labels = $supplierStatus->getLabels();

    expect($labels)->toBeArray()
        ->and($labels)->toHaveCount(2)
        ->and($labels[0]['label'])->toBe('Pending document validation')
        ->and($labels[0]['color'])->toBe('warning')
        ->and($labels[1]['label'])->toBe('Pending address validation')
        ->and($labels[1]['color'])->toBe('warning');
});

test('getLabels returns no documents uploaded when no attachments exist', function () {
    // Create required documents
    Document::factory()->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create validated address
    Address::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);
    $labels = $supplierStatus->getLabels();

    expect($labels[0]['label'])->toBe('No documents uploaded')
        ->and($labels[0]['color'])->toBe('warning');
});

test('getLabels returns no addresses uploaded when no addresses exist', function () {
    // Create required documents
    Document::factory()->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create validated attachment
    Attachment::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);
    $labels = $supplierStatus->getLabels();

    expect($labels[1]['label'])->toBe('No addresses uploaded')
        ->and($labels[1]['color'])->toBe('warning');
});

test('handles mixed validation states correctly', function () {
    // Create required documents
    Document::factory()->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create one validated and one unvalidated address
    Address::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);
    Address::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => false,
    ]);

    // Create one validated and one unvalidated attachment
    Attachment::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);
    Attachment::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => false,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();

    $labels = $supplierStatus->getLabels();
    expect($labels[0]['label'])->toBe('Pending document validation')
        ->and($labels[1]['label'])->toBe('Pending address validation');
});

test('handles insufficient required documents correctly', function () {
    // Create multiple required documents
    Document::factory()->count(2)->create([
        'procurement_type' => ProcType::GOODS->value,
        'is_required' => true,
    ]);

    // Create only one attachment (insufficient)
    Attachment::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    // Create validated address
    Address::factory()->create([
        'supplier_id' => $this->supplier->id,
        'is_validated' => true,
    ]);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();

    $labels = $supplierStatus->getLabels();
    expect($labels[0]['label'])->toBe('Other required documents are missing')
        ->and($labels[0]['color'])->toBe('warning');
});
