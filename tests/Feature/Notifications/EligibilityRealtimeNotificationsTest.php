<?php

use App\Enums\ProcType;
use App\Enums\Status;
use App\Enums\UserRole;
use App\Filament\Resources\Attachments\Actions\UpdateStatus;
use App\Helpers\EligibilityChangeNotifier;
use App\Models\Address;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\LobCategory;
use App\Models\LobSubcategory;
use App\Models\Status as StatusModel;
use App\Models\Supplier;
use App\Models\SupplierLob;
use App\Models\User;
use App\Notifications\EligibilityChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('sends eligibility notification in realtime when eligibility changes', function () {
    Notification::fake();

    Document::query()->delete();

    $admin = User::factory()->create([
        'role' => UserRole::Administrator,
    ]);

    $supplierUser = User::factory()->create([
        'role' => UserRole::Supplier,
    ]);

    $supplier = Supplier::factory()->create([
        'user_id' => $supplierUser->id,
        'supplier_type' => ProcType::GOODS,
    ]);

    $document = Document::factory()->create([
        'procurement_type' => [ProcType::GOODS->value],
        'is_required' => true,
    ]);

    $attachment = Attachment::query()->create([
        'supplier_id' => $supplier->id,
        'document_id' => $document->id,
        'file_path' => 'attachments/test-eligible.pdf',
        'file_size' => 1024,
        'validity_date' => now()->addYear()->toDateString(),
    ]);

    $address = Address::factory()->create([
        'supplier_id' => $supplier->id,
    ]);

    $lobCategory = LobCategory::query()->create([
        'title' => 'Category A',
        'description' => 'Category',
    ]);

    $lobSubcategory = LobSubcategory::query()->create([
        'lob_category_id' => $lobCategory->id,
        'title' => 'Subcategory A',
        'description' => 'Subcategory',
    ]);

    SupplierLob::query()->create([
        'supplier_id' => $supplier->id,
        'lob_category_id' => $lobCategory->id,
        'lob_subcategory_id' => $lobSubcategory->id,
    ]);

    StatusModel::withoutEvents(function () use ($admin, $supplier, $attachment, $address): void {
        $attachment->statuses()->create([
            'user_id' => $admin->id,
            'status' => Status::Validated,
            'status_date' => now(),
            'remarks' => 'validated attachment',
        ]);

        $address->statuses()->create([
            'user_id' => $admin->id,
            'status' => Status::Validated,
            'status_date' => now(),
            'remarks' => 'validated address',
        ]);

        $supplier->lob_statuses()->create([
            'user_id' => $admin->id,
            'status' => Status::Validated,
            'status_date' => now(),
            'remarks' => 'validated lobs',
        ]);
    });

    expect(EligibilityChangeNotifier::currentEligibility($supplier))->toBeTrue();

    $attachment->statuses()->create([
        'user_id' => $admin->id,
        'status' => Status::Rejected,
        'status_date' => now(),
        'remarks' => 'rejected attachment',
    ]);

    expect(EligibilityChangeNotifier::currentEligibility($supplier))->toBeFalse();

    Notification::assertSentTo($supplierUser, EligibilityChanged::class);
});

it('does not send eligibility notification when eligibility does not change', function () {
    Notification::fake();

    Document::query()->delete();

    $admin = User::factory()->create([
        'role' => UserRole::Administrator,
    ]);

    $supplierUser = User::factory()->create([
        'role' => UserRole::Supplier,
    ]);

    $supplier = Supplier::factory()->create([
        'user_id' => $supplierUser->id,
        'supplier_type' => ProcType::GOODS,
    ]);

    $document = Document::factory()->create([
        'procurement_type' => [ProcType::GOODS->value],
        'is_required' => true,
    ]);

    $attachment = Attachment::query()->create([
        'supplier_id' => $supplier->id,
        'document_id' => $document->id,
        'file_path' => 'attachments/test-pending.pdf',
        'file_size' => 1024,
        'validity_date' => now()->addYear()->toDateString(),
    ]);

    $attachment->statuses()->create([
        'user_id' => $admin->id,
        'status' => Status::PendingReview,
        'status_date' => now(),
        'remarks' => 'still pending',
    ]);

    Notification::assertNothingSent();
});

it('sends eligibility notification when attachment status is updated through the admin action', function () {
    Notification::fake();

    $admin = User::factory()->create([
        'role' => UserRole::Administrator,
    ]);

    $supplierUser = User::factory()->create([
        'role' => UserRole::Supplier,
    ]);

    $supplier = Supplier::factory()->create([
        'user_id' => $supplierUser->id,
        'supplier_type' => ProcType::GOODS,
    ]);

    $document = Document::factory()->create([
        'procurement_type' => [ProcType::GOODS->value],
        'is_required' => true,
    ]);

    $attachment = Attachment::query()->create([
        'supplier_id' => $supplier->id,
        'document_id' => $document->id,
        'file_path' => 'attachments/admin-action.pdf',
        'file_size' => 1024,
        'validity_date' => now()->addYear()->toDateString(),
    ]);

    $address = Address::factory()->create([
        'supplier_id' => $supplier->id,
    ]);

    $lobCategory = LobCategory::query()->create([
        'title' => 'Category A',
        'description' => 'Category',
    ]);

    $lobSubcategory = LobSubcategory::query()->create([
        'lob_category_id' => $lobCategory->id,
        'title' => 'Subcategory A',
        'description' => 'Subcategory',
    ]);

    SupplierLob::query()->create([
        'supplier_id' => $supplier->id,
        'lob_category_id' => $lobCategory->id,
        'lob_subcategory_id' => $lobSubcategory->id,
    ]);

    StatusModel::withoutEvents(function () use ($admin, $attachment, $address): void {
        $attachment->statuses()->create([
            'user_id' => $admin->id,
            'status' => Status::PendingReview,
            'status_date' => now()->subDay(),
            'remarks' => 'pending attachment',
        ]);

        $address->statuses()->create([
            'user_id' => $admin->id,
            'status' => Status::Validated,
            'status_date' => now()->subDay(),
            'remarks' => 'validated address',
        ]);
    });

    StatusModel::withoutEvents(function () use ($admin, $supplier): void {
        $supplier->lob_statuses()->create([
            'user_id' => $admin->id,
            'status' => Status::Validated,
            'status_date' => now()->subDay(),
            'remarks' => 'validated lobs',
        ]);
    });

    request()->setUserResolver(fn () => $admin);

    UpdateStatus::save($attachment, [
        'status' => Status::Validated,
        'remarks' => 'validated attachment',
    ]);

    Notification::assertSentTo($supplierUser, EligibilityChanged::class);

    expect(EligibilityChangeNotifier::currentEligibility($supplier))->toBeTrue();
});
