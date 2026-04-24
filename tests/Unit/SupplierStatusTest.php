<?php

use App\Enums\ProcType;
use App\Enums\Status;
use App\Helpers\SupplierStatus;
use App\Models\Address;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\LobCategory;
use App\Models\LobSubcategory;
use App\Models\Status as StatusModel;
use App\Models\Supplier;
use App\Models\SupplierLob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->supplier = Supplier::factory()->create([
        'supplier_type' => ProcType::GOODS,
    ]);

    $this->statusUser = User::factory()->create();

    seedValidatedLinesOfBusiness($this->statusUser, $this->supplier);
});

function seedValidatedLinesOfBusiness(User $user, Supplier $supplier): void
{
    $category = LobCategory::query()->create([
        'title' => 'General Merchandise',
        'description' => 'General supply category',
    ]);

    $subcategory = LobSubcategory::query()->create([
        'lob_category_id' => $category->id,
        'title' => 'Office Supplies',
        'description' => 'Office and stationery items',
    ]);

    SupplierLob::query()->create([
        'supplier_id' => $supplier->id,
        'lob_category_id' => $category->id,
        'lob_subcategory_id' => $subcategory->id,
    ]);

    StatusModel::query()->create([
        'user_id' => $user->id,
        'statusable_id' => $supplier->id,
        'statusable_type' => Supplier::class,
        'status' => Status::Validated,
        'remarks' => 'Validated lines of business',
        'status_date' => now(),
    ]);
}

function seedAddressStatus(User $user, Supplier $supplier, Status $status): void
{
    $address = Address::factory()->create([
        'supplier_id' => $supplier->id,
    ]);

    StatusModel::query()->create([
        'user_id' => $user->id,
        'statusable_id' => $address->id,
        'statusable_type' => Address::class,
        'status' => $status,
        'remarks' => 'Address validation state',
        'status_date' => now(),
    ]);
}

function seedAttachmentStatus(User $user, Supplier $supplier, Status $status): void
{
    $attachment = Attachment::query()->create([
        'supplier_id' => $supplier->id,
        'document_id' => null,
        'file_path' => 'attachments/test.pdf',
        'file_size' => 1024,
        'validity_date' => now()->addYear()->toDateString(),
    ]);

    StatusModel::query()->create([
        'user_id' => $user->id,
        'statusable_id' => $attachment->id,
        'statusable_type' => Attachment::class,
        'status' => $status,
        'remarks' => 'Attachment validation state',
        'status_date' => now(),
    ]);
}

function seedRequiredDocument(int $count = 1): void
{
    Document::factory()->count($count)->create([
        'procurement_type' => [ProcType::GOODS->value],
        'is_required' => true,
    ]);
}

test('constructor initializes validation states correctly', function () {
    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();
});

test('isFullyValidated returns true when both address and document validations pass', function () {
    seedRequiredDocument();
    seedAddressStatus($this->statusUser, $this->supplier, Status::Validated);
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Validated);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeTrue();
});

test('isFullyValidated returns false when address validation fails', function () {
    seedRequiredDocument();
    seedAddressStatus($this->statusUser, $this->supplier, Status::Rejected);
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Validated);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();
});

test('isFullyValidated returns false when document validation fails', function () {
    seedRequiredDocument();
    seedAddressStatus($this->statusUser, $this->supplier, Status::Validated);
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Rejected);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();
});

test('isFullyValidated returns false when no addresses exist', function () {
    seedRequiredDocument();
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Validated);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();
});

test('isFullyValidated returns false when no attachments exist', function () {
    seedRequiredDocument();
    seedAddressStatus($this->statusUser, $this->supplier, Status::Validated);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();
});

test('getLabels returns correct array structure with document and address labels', function () {
    seedRequiredDocument();
    seedAddressStatus($this->statusUser, $this->supplier, Status::Validated);
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Validated);

    $supplierStatus = new SupplierStatus($this->supplier);
    $labels = $supplierStatus->getLabels();

    expect($labels)->toBeArray()
        ->and($labels)->toHaveCount(3)
        ->and($labels[0])->toHaveKeys(['label', 'color'])
        ->and($labels[1])->toHaveKeys(['label', 'color'])
        ->and($labels[2])->toHaveKeys(['label', 'color'])
        ->and($labels[0]['label'])->toBe('Documents Validated')
        ->and($labels[0]['color'])->toBe('success')
        ->and($labels[1]['label'])->toBe('Address Validated')
        ->and($labels[1]['color'])->toBe('success')
        ->and($labels[2]['label'])->toBe('Lines of Business Validated')
        ->and($labels[2]['color'])->toBe('success');
});

test('getLabels returns warning labels when validations fail', function () {
    seedRequiredDocument();
    seedAddressStatus($this->statusUser, $this->supplier, Status::Validated);
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Validated);
    seedAddressStatus($this->statusUser, $this->supplier, Status::Rejected);
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Rejected);

    $supplierStatus = new SupplierStatus($this->supplier);
    $labels = $supplierStatus->getLabels();

    expect($labels)->toBeArray()
        ->and($labels)->toHaveCount(3)
        ->and($labels[0]['label'])->toBe('Non-compliant as to document requirements')
        ->and($labels[0]['color'])->toBe('warning')
        ->and($labels[1]['label'])->toBe('Pending address validation')
        ->and($labels[1]['color'])->toBe('warning')
        ->and($labels[2]['label'])->toBe('Lines of Business Validated')
        ->and($labels[2]['color'])->toBe('success');
});

test('getLabels returns no documents uploaded when no attachments exist', function () {
    seedRequiredDocument();
    seedAddressStatus($this->statusUser, $this->supplier, Status::Validated);

    $supplierStatus = new SupplierStatus($this->supplier);
    $labels = $supplierStatus->getLabels();

    expect($labels[0]['label'])->toBe('No documents uploaded')
        ->and($labels[0]['color'])->toBe('warning')
        ->and($labels[2]['label'])->toBe('Lines of Business Validated')
        ->and($labels[2]['color'])->toBe('success');
});

test('getLabels returns no addresses uploaded when no addresses exist', function () {
    seedRequiredDocument();
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Validated);

    $supplierStatus = new SupplierStatus($this->supplier);
    $labels = $supplierStatus->getLabels();

    expect($labels[1]['label'])->toBe('No addresses provided')
        ->and($labels[1]['color'])->toBe('warning')
        ->and($labels[2]['label'])->toBe('Lines of Business Validated')
        ->and($labels[2]['color'])->toBe('success');
});

test('handles mixed validation states correctly', function () {
    seedRequiredDocument();
    seedAddressStatus($this->statusUser, $this->supplier, Status::Validated);
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Validated);
    seedAddressStatus($this->statusUser, $this->supplier, Status::Rejected);
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Rejected);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();

    $labels = $supplierStatus->getLabels();
    expect($labels[0]['label'])->toBe('Non-compliant as to document requirements')
        ->and($labels[1]['label'])->toBe('Pending address validation')
        ->and($labels[2]['label'])->toBe('Lines of Business Validated');
});

test('handles insufficient required documents correctly', function () {
    seedRequiredDocument(2);
    seedAddressStatus($this->statusUser, $this->supplier, Status::Validated);
    seedAttachmentStatus($this->statusUser, $this->supplier, Status::Validated);

    $supplierStatus = new SupplierStatus($this->supplier);

    expect($supplierStatus->isFullyValidated())->toBeFalse();

    $labels = $supplierStatus->getLabels();
    expect($labels[0]['label'])->toBe('Other required documents are missing')
        ->and($labels[0]['color'])->toBe('warning')
        ->and($labels[2]['label'])->toBe('Lines of Business Validated');
});
