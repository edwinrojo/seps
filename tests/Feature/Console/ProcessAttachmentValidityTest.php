<?php

use App\Console\Commands\ProcessAttachmentValidity;
use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\Status as StatusModel;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\DocumentExpired;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('marks expired attachments with an expired status', function () {
    $admin = User::query()->create([
        'first_name' => 'System',
        'last_name' => 'Admin',
        'role' => UserRole::Administrator,
        'email' => 'admin@example.com',
        'contact_number' => '09170000000',
        'password' => 'password',
    ]);

    $supplier = Supplier::query()->create([
        'business_name' => 'Supplier One',
        'email' => 'supplier.one@example.com',
        'mobile_number' => '09170000001',
        'supplier_type' => 'goods',
    ]);

    $attachment = Attachment::query()->create([
        'supplier_id' => $supplier->id,
        'document_id' => null,
        'file_path' => 'attachments/test.pdf',
        'file_size' => 1024,
        'validity_date' => now()->subDay()->toDateString(),
    ]);

    StatusModel::query()->create([
        'user_id' => $admin->id,
        'statusable_id' => $attachment->id,
        'statusable_type' => Attachment::class,
        'status' => Status::Validated->value,
        'remarks' => 'Initial status',
        'status_date' => now()->subDays(2),
    ]);

    $this->artisan(ProcessAttachmentValidity::class)
        ->assertSuccessful();

    expect(
        StatusModel::query()
            ->where('statusable_type', Attachment::class)
            ->where('statusable_id', $attachment->id)
            ->latest('status_date')
            ->first()
            ?->status
    )->toBe(Status::Expired);
});

it('sends supplier notification for expired documents', function () {
    Notification::fake();

    $admin = User::query()->create([
        'first_name' => 'System',
        'last_name' => 'Admin',
        'role' => UserRole::Administrator,
        'email' => 'admin@example.com',
        'contact_number' => '09170000000',
        'password' => 'password',
    ]);

    $supplierUser = User::query()->create([
        'first_name' => 'Supplier',
        'last_name' => 'User',
        'role' => UserRole::Supplier,
        'email' => 'supplier.user@example.com',
        'contact_number' => '09170000001',
        'password' => 'password',
    ]);

    $supplier = Supplier::factory()->create([
        'user_id' => $supplierUser->id,
    ]);

    $document = Document::factory()->create();

    $attachment = Attachment::query()->create([
        'supplier_id' => $supplier->id,
        'document_id' => $document->id,
        'file_path' => 'attachments/test-expired.pdf',
        'file_size' => 1024,
        'validity_date' => now()->subDay()->toDateString(),
    ]);

    StatusModel::query()->create([
        'user_id' => $admin->id,
        'statusable_id' => $attachment->id,
        'statusable_type' => Attachment::class,
        'status' => Status::Validated->value,
        'remarks' => 'Initially valid',
        'status_date' => now()->subDays(2),
    ]);

    $this->artisan(ProcessAttachmentValidity::class)
        ->assertSuccessful();

    Notification::assertSentTo($supplierUser, DocumentExpired::class);
});

it('sends supplier notification when supplier user is resolved by email fallback', function () {
    Notification::fake();

    $admin = User::query()->create([
        'first_name' => 'System',
        'last_name' => 'Admin',
        'role' => UserRole::Administrator,
        'email' => 'admin-fallback@example.com',
        'contact_number' => '09170000022',
        'password' => 'password',
    ]);

    $supplierUser = User::query()->create([
        'first_name' => 'Fallback',
        'last_name' => 'Supplier',
        'role' => UserRole::Supplier,
        'email' => 'owner-fallback@example.com',
        'contact_number' => '09170000023',
        'password' => 'password',
    ]);

    $supplier = Supplier::factory()->create([
        'user_id' => null,
        'email' => $supplierUser->email,
    ]);

    $document = Document::factory()->create();

    $attachment = Attachment::query()->create([
        'supplier_id' => $supplier->id,
        'document_id' => $document->id,
        'file_path' => 'attachments/test-email-fallback.pdf',
        'file_size' => 1024,
        'validity_date' => now()->subDay()->toDateString(),
    ]);

    StatusModel::query()->create([
        'user_id' => $admin->id,
        'statusable_id' => $attachment->id,
        'statusable_type' => Attachment::class,
        'status' => Status::Validated->value,
        'remarks' => 'initially valid',
        'status_date' => now()->subDays(2),
    ]);

    $this->artisan(ProcessAttachmentValidity::class)
        ->assertSuccessful();

    Notification::assertSentTo($supplierUser, DocumentExpired::class);

    expect($supplier->fresh()->user_id)->toBe($supplierUser->id);
});
