<?php

use App\Console\Commands\NotifyEligibilityUpdates;
use App\Enums\UserRole;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\EligibilityChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('sends eligibility update notification to supplier users', function () {
    Notification::fake();

    $supplierUser = User::query()->create([
        'first_name' => 'Supplier',
        'last_name' => 'User',
        'role' => UserRole::Supplier,
        'email' => 'supplier@example.com',
        'contact_number' => '09170000011',
        'password' => 'password',
    ]);

    Supplier::factory()->create([
        'user_id' => $supplierUser->id,
    ]);

    $this->artisan(NotifyEligibilityUpdates::class)
        ->assertSuccessful();

    Notification::assertSentTo($supplierUser, EligibilityChanged::class);
});

it('does not send duplicate eligibility notifications when status is unchanged', function () {
    $supplierUser = User::query()->create([
        'first_name' => 'Supplier',
        'last_name' => 'User',
        'role' => UserRole::Supplier,
        'email' => 'supplier2@example.com',
        'contact_number' => '09170000012',
        'password' => 'password',
    ]);

    Supplier::factory()->create([
        'user_id' => $supplierUser->id,
    ]);

    $this->artisan(NotifyEligibilityUpdates::class)
        ->assertSuccessful();

    $this->artisan(NotifyEligibilityUpdates::class)
        ->assertSuccessful();

    expect(
        $supplierUser->notifications()->where('type', EligibilityChanged::class)->count()
    )->toBe(1);
});
