<?php

use App\Filament\Resources\SoftwareKeys\Schemas\SoftwareKeyForm;
use App\Models\Package;
use App\Models\SoftwareKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('allows only administrators to access the Filament dashboard', function () {
    $admin = User::factory()->admin()->create();
    $staff = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('filament.admin.pages.dashboard'))
        ->assertOk();

    $this->actingAs($staff)
        ->get(route('filament.admin.pages.dashboard'))
        ->assertForbidden();
});

it('stores software key secrets encrypted at rest and hides them from array serialization', function () {
    $package = Package::query()->create([
        'slug' => 'bao-mat-1-ngay',
        'service_code' => 'vietmap',
        'name' => 'Goi bao mat',
        'short_name' => 'Bao mat',
        'description' => 'Demo package',
        'duration_hours' => 24,
        'duration_label' => '1 ngay',
        'price' => 19000,
        'compare_at_price' => 29000,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $softwareKey = SoftwareKey::query()->create([
        'package_id' => $package->id,
        'reference' => 'SEC-001',
        'label' => 'Key bao mat',
        'username' => 'secure@example.com',
        'password' => 'super-secret',
        'license_key' => 'KEY-SECRET-001',
        'status' => SoftwareKey::STATUS_AVAILABLE,
        'is_active' => true,
    ]);

    $rawRecord = DB::table('software_keys')->where('id', $softwareKey->id)->first();

    expect($rawRecord->username)->not->toBe('secure@example.com')
        ->and($rawRecord->password)->not->toBe('super-secret')
        ->and($rawRecord->license_key)->not->toBe('KEY-SECRET-001')
        ->and($softwareKey->fresh()->username)->toBe('secure@example.com')
        ->and($softwareKey->fresh()->password)->toBe('super-secret')
        ->and($softwareKey->fresh()->license_key)->toBe('KEY-SECRET-001')
        ->and($softwareKey->toArray())->not->toHaveKeys(['username', 'password', 'license_key']);
});

it('does not render plaintext secrets on the Filament software key edit page', function () {
    $admin = User::factory()->admin()->create();
    $package = Package::query()->create([
        'slug' => 'bao-mat-2-ngay',
        'service_code' => 'vietmap',
        'name' => 'Goi admin',
        'short_name' => 'Admin',
        'description' => 'Demo package',
        'duration_hours' => 48,
        'duration_label' => '2 ngay',
        'price' => 39000,
        'compare_at_price' => 49000,
        'is_active' => true,
        'sort_order' => 1,
    ]);
    $softwareKey = SoftwareKey::query()->create([
        'package_id' => $package->id,
        'reference' => 'SEC-002',
        'label' => 'Key admin',
        'username' => 'hidden@example.com',
        'password' => 'hidden-password',
        'license_key' => 'HIDDEN-LICENSE-KEY',
        'status' => SoftwareKey::STATUS_AVAILABLE,
        'is_active' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('filament.admin.resources.software-keys.edit', ['record' => $softwareKey]))
        ->assertOk()
        ->assertDontSee('hidden@example.com')
        ->assertDontSee('hidden-password')
        ->assertDontSee('HIDDEN-LICENSE-KEY');
});

it('sanitizes blank sensitive fields so edit forms keep the current encrypted values', function () {
    $payload = SoftwareKeyForm::sanitizeSensitiveData([
        'username' => '   ',
        'password' => '',
        'license_key' => null,
        'label' => 'Tai khoan moi',
    ]);

    expect($payload)->toBe([
        'label' => 'Tai khoan moi',
    ]);
});

it('keeps only freshly entered sensitive fields so they can be re-encrypted on save', function () {
    $payload = SoftwareKeyForm::sanitizeSensitiveData([
        'username' => '  next@example.com  ',
        'password' => ' next-password ',
        'license_key' => ' NEXT-LICENSE ',
    ]);

    expect($payload)->toBe([
        'username' => 'next@example.com',
        'password' => 'next-password',
        'license_key' => 'NEXT-LICENSE',
    ]);
});
