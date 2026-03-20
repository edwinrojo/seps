<?php

use App\Enums\ProcType;
use App\Enums\UserRole;
use App\Models\LobCategory;
use App\Models\LobSubcategory;
use App\Models\Supplier;
use App\Models\SupplierLob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('requires sanctum authentication to list suppliers', function () {
    getJson('/api/suppliers')->assertUnauthorized();
});

it('returns suppliers with complete lob details', function () {
    $user = User::query()->create([
        'first_name' => 'System',
        'last_name' => 'Admin',
        'role' => UserRole::Administrator,
        'email' => 'admin@example.com',
        'contact_number' => '09171234567',
        'password' => Hash::make('password'),
    ]);

    $supplier = Supplier::query()->create([
        'business_name' => 'Alpha Supplies',
        'email' => 'alpha@example.com',
        'mobile_number' => '09170000001',
        'supplier_type' => ProcType::GOODS,
    ]);

    $category = LobCategory::query()->create([
        'title' => 'Construction',
    ]);

    $subcategory = LobSubcategory::query()->create([
        'lob_category_id' => $category->id,
        'title' => 'Civil Works',
    ]);

    SupplierLob::query()->create([
        'supplier_id' => $supplier->id,
        'lob_category_id' => $category->id,
        'lob_subcategory_id' => $subcategory->id,
    ]);

    Sanctum::actingAs($user);

    $response = getJson('/api/suppliers');

    $response
        ->assertOk()
        ->assertJsonCount(1, 'suppliers')
        ->assertJsonPath('suppliers.0.business_name', 'Alpha Supplies')
        ->assertJsonPath('suppliers.0.supplier_type', ProcType::GOODS->value)
        ->assertJsonPath('suppliers.0.lobs.0.lob_category.title', 'Construction')
        ->assertJsonPath('suppliers.0.lobs.0.lob_subcategory.title', 'Civil Works');
});
