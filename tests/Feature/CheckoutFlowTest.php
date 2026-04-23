<?php

use App\Mail\OrderCredentialsMail;
use App\Models\Order;
use App\Models\Package;
use App\Models\SoftwareKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('creates a pending order and reserves a software key', function () {
    $package = Package::query()->create([
        'slug' => 'vietmap-1-ngay',
        'service_code' => 'vietmap',
        'name' => 'VIETMAP LIVE PRO 1 ngay',
        'short_name' => 'Vietmap 1 ngay',
        'description' => 'Demo package',
        'duration_hours' => 24,
        'duration_label' => '1 ngay',
        'price' => 19000,
        'compare_at_price' => 30000,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $key = SoftwareKey::query()->create([
        'package_id' => $package->id,
        'reference' => 'VM-001',
        'label' => 'Tai khoan demo',
        'username' => 'demo@example.com',
        'password' => 'secret',
        'status' => SoftwareKey::STATUS_AVAILABLE,
        'is_active' => true,
    ]);

    $response = $this->post('/thanh-toan', [
        'package_id' => $package->id,
        'customer_email' => 'buyer@example.com',
    ]);

    $order = Order::query()->first();

    $response->assertRedirect(route('checkout.show', $order->order_number));

    expect($order)
        ->payment_status->toBe(Order::PAYMENT_PENDING)
        ->customer_email->toBe('buyer@example.com')
        ->software_key_id->toBe($key->id);

    expect($key->fresh())
        ->status->toBe(SoftwareKey::STATUS_RESERVED)
        ->order_id->toBe($order->id);
});

it('marks an order as paid from sepay webhook and delivers the reserved key', function () {
    Mail::fake();

    config()->set('services.sepay.webhook_api_key', 'test-key');

    $package = Package::query()->create([
        'slug' => 'vietmap-7-ngay',
        'service_code' => 'vietmap',
        'name' => 'VIETMAP LIVE PRO 7 ngay',
        'short_name' => 'Vietmap 7 ngay',
        'description' => 'Demo package',
        'duration_hours' => 168,
        'duration_label' => '7 ngay',
        'price' => 69000,
        'compare_at_price' => 100000,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $key = SoftwareKey::query()->create([
        'package_id' => $package->id,
        'reference' => 'VM-777',
        'label' => 'Tai khoan giao khach',
        'username' => 'paid@example.com',
        'password' => 'super-secret',
        'license_key' => 'ABC-123-XYZ',
        'status' => SoftwareKey::STATUS_AVAILABLE,
        'is_active' => true,
    ]);

    $this->post('/thanh-toan', [
        'package_id' => $package->id,
        'customer_email' => 'buyer@example.com',
    ])->assertRedirect();

    $order = Order::query()->firstOrFail();

    $response = $this->withHeaders([
        'Authorization' => 'Apikey test-key',
    ])->postJson('/api/webhooks/sepay', [
        'id' => 123456,
        'gateway' => 'Vietcombank',
        'transactionDate' => '2026-04-23 10:00:00',
        'accountNumber' => '123456789',
        'code' => $order->order_number,
        'content' => $order->order_number,
        'transferType' => 'in',
        'transferAmount' => 69000,
        'referenceCode' => 'VCB.123456',
        'description' => 'Payment for ' . $order->order_number,
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
            'matched_order_number' => $order->order_number,
        ]);

    expect($order->fresh())
        ->payment_status->toBe(Order::PAYMENT_PAID)
        ->fulfillment_status->toBe(Order::FULFILLMENT_FULFILLED)
        ->provider_transaction_id->toBe('123456');

    expect($key->fresh())
        ->status->toBe(SoftwareKey::STATUS_DELIVERED)
        ->order_id->toBe($order->id);

    Mail::assertSent(OrderCredentialsMail::class, fn (OrderCredentialsMail $mail) => $mail->order->is($order->fresh()));
});
