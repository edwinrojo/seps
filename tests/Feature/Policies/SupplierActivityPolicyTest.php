<?php

use App\Enums\ProcType;
use App\Enums\UserRole;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows admin to view supplier activities', function () {
    $admin = User::factory()->create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'role' => UserRole::Administrator,
        'contact_number' => '09170000001',
    ]);

    $supplier = Supplier::query()->create([
        'business_name' => 'Supplier One',
        'email' => 'supplier-one@example.com',
        'mobile_number' => '09170000011',
        'supplier_type' => ProcType::GOODS,
    ]);

    actingAs($admin);

    expect($admin->can('viewActivities', $supplier))->toBeTrue();
});

it('denies supplier role from viewing supplier activities', function () {
    $supplierUser = User::factory()->create([
        'first_name' => 'Supplier',
        'last_name' => 'User',
        'role' => UserRole::Supplier,
        'contact_number' => '09170000002',
    ]);

    $supplier = Supplier::query()->create([
        'business_name' => 'Supplier Two',
        'email' => 'supplier-two@example.com',
        'mobile_number' => '09170000022',
        'supplier_type' => ProcType::GOODS,
    ]);

    actingAs($supplierUser);

    expect($supplierUser->can('viewActivities', $supplier))->toBeFalse();
});
