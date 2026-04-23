<?php

namespace App\Services\Orders;

use App\Mail\OrderCredentialsMail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Package;
use App\Models\PaymentWebhook;
use App\Models\SoftwareKey;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    public function resolvePackageFromRequest(Request $request): Package
    {
        $packageId = $request->integer('package') ?: $request->integer('package_id') ?: $request->integer('price_id');

        if ($packageId) {
            return Package::query()
                ->active()
                ->findOrFail($packageId);
        }

        $serviceCode = (string) $request->string('service', 'vietmap');
        $hours = $request->integer('hours');

        abort_unless($hours > 0, 404);

        return Package::query()
            ->active()
            ->where('service_code', $serviceCode)
            ->where('duration_hours', $hours)
            ->firstOrFail();
    }

    public function findApplicableCoupon(?string $code, int $amount): ?Coupon
    {
        $code = Str::upper(trim((string) $code));

        if ($code === '') {
            return null;
        }

        $coupon = Coupon::query()
            ->active()
            ->whereRaw('UPPER(code) = ?', [$code])
            ->first();

        if (! $coupon || ! $coupon->canBeAppliedTo($amount)) {
            return null;
        }

        return $coupon;
    }

    public function buildAmountSummary(Package $package, ?Coupon $coupon = null): array
    {
        $originalAmount = $package->price;
        $discountAmount = $coupon?->calculateDiscount($originalAmount) ?? 0;

        return [
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'amount' => max(0, $originalAmount - $discountAmount),
        ];
    }

    public function createPendingOrder(
        Package $package,
        ?string $customerEmail = null,
        ?Coupon $coupon = null,
        ?string $customerIp = null,
    ): Order {
        return DB::transaction(function () use ($package, $customerEmail, $coupon, $customerIp): Order {
            $this->releaseExpiredReservations();

            /** @var SoftwareKey|null $softwareKey */
            $softwareKey = SoftwareKey::query()
                ->where('package_id', $package->id)
                ->available()
                ->lockForUpdate()
                ->first();

            if (! $softwareKey) {
                throw new RuntimeException('Hiện gói này đã hết key khả dụng. Vui lòng nạp thêm key trong Filament.');
            }

            $amounts = $this->buildAmountSummary($package, $coupon);

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'package_id' => $package->id,
                'coupon_id' => $coupon?->id,
                'software_key_id' => $softwareKey->id,
                'customer_email' => $customerEmail ?: null,
                'customer_ip' => $customerIp,
                'original_amount' => $amounts['original_amount'],
                'discount_amount' => $amounts['discount_amount'],
                'amount' => $amounts['amount'],
                'coupon_code' => $coupon?->code,
                'payment_status' => Order::PAYMENT_PENDING,
                'fulfillment_status' => Order::FULFILLMENT_PENDING,
                'expires_at' => now()->addMinutes(config('services.sepay.order_expiration_minutes', 30)),
                'meta' => [
                    'transfer_content' => null,
                ],
            ]);

            $softwareKey->forceFill([
                'order_id' => $order->id,
                'status' => SoftwareKey::STATUS_RESERVED,
                'reserved_at' => now(),
            ])->save();

            return $order->fresh(['package', 'coupon', 'softwareKey']);
        });
    }

    public function expirePendingOrderIfNeeded(Order $order): Order
    {
        if (
            $order->payment_status !== Order::PAYMENT_PENDING
            || ! $order->isExpired()
        ) {
            return $order;
        }

        return DB::transaction(function () use ($order): Order {
            $order->refresh();

            if (
                $order->payment_status !== Order::PAYMENT_PENDING
                || ! $order->isExpired()
            ) {
                return $order;
            }

            $order->forceFill([
                'payment_status' => Order::PAYMENT_EXPIRED,
                'software_key_id' => null,
            ])->save();

            if ($order->softwareKey && $order->softwareKey->order_id === $order->id) {
                $order->softwareKey->forceFill([
                    'order_id' => null,
                    'status' => SoftwareKey::STATUS_AVAILABLE,
                    'reserved_at' => null,
                ])->save();
            }

            return $order->fresh(['package', 'coupon', 'softwareKey']);
        });
    }

    public function buildSePayQrUrl(Order $order): string
    {
        $baseUrl = 'https://qr.sepay.vn/img';

        $query = array_filter([
            'acc' => config('services.sepay.account_number'),
            'bank' => config('services.sepay.bank_code'),
            'amount' => $order->amount,
            'des' => $order->order_number,
            'template' => config('services.sepay.qr_template'),
        ], fn (mixed $value): bool => filled($value));

        return $baseUrl . '?' . http_build_query($query);
    }

    public function processWebhook(Request $request): PaymentWebhook
    {
        $headers = collect($request->headers->all())
            ->map(fn (array $values): string => implode(', ', $values))
            ->all();
        $payload = $request->all();
        $providerTransactionId = Arr::get($payload, 'id');
        $matchedOrderNumber = $this->extractOrderNumberFromPayload($payload);
        $isValid = $this->hasValidWebhookAuthorization($request);

        $webhook = PaymentWebhook::query()->create([
            'provider' => 'sepay',
            'provider_transaction_id' => $providerTransactionId,
            'matched_order_number' => $matchedOrderNumber,
            'headers' => $headers,
            'payload' => $payload,
            'is_valid' => $isValid,
        ]);

        if (! $isValid) {
            return $webhook;
        }

        if (Arr::get($payload, 'transferType') !== 'in') {
            return tap($webhook)->update(['processed_at' => now()]);
        }

        $order = Order::query()
            ->where('order_number', $matchedOrderNumber)
            ->first();

        if (! $order) {
            return tap($webhook)->update(['processed_at' => now()]);
        }

        $this->expirePendingOrderIfNeeded($order);
        $order->refresh();

        if ($order->payment_status === Order::PAYMENT_PAID) {
            return tap($webhook)->update([
                'order_id' => $order->id,
                'processed_at' => now(),
            ]);
        }

        $transferAmount = (int) Arr::get($payload, 'transferAmount', 0);

        if ($transferAmount < $order->amount) {
            return tap($webhook)->update([
                'order_id' => $order->id,
                'processed_at' => now(),
            ]);
        }

        DB::transaction(function () use ($order, $payload, $webhook): void {
            $order->refresh();

            if ($order->payment_status !== Order::PAYMENT_PENDING) {
                return;
            }

            $paidAt = Arr::get($payload, 'transactionDate')
                ? Carbon::createFromFormat('Y-m-d H:i:s', (string) Arr::get($payload, 'transactionDate'))
                : now();

            $meta = $order->meta ?? [];
            $meta['sepay_payload'] = $payload;

            $order->forceFill([
                'payment_status' => Order::PAYMENT_PAID,
                'payment_gateway' => Arr::get($payload, 'gateway'),
                'provider_transaction_id' => (string) Arr::get($payload, 'id'),
                'paid_at' => $paidAt,
                'meta' => $meta,
            ])->save();

            if ($order->coupon) {
                $order->coupon->increment('used_count');
            }

            $this->fulfillPaidOrder($order);

            $webhook->forceFill([
                'order_id' => $order->id,
                'processed_at' => now(),
            ])->save();
        });

        return $webhook->fresh(['order']);
    }

    public function fulfillPaidOrder(Order $order): Order
    {
        if (! $order->canFulfill()) {
            return $order->fresh(['package', 'softwareKey']);
        }

        return DB::transaction(function () use ($order): Order {
            $order->refresh();

            if (! $order->canFulfill()) {
                return $order;
            }

            $softwareKey = $order->softwareKey;

            if (! $softwareKey) {
                throw new RuntimeException('Đơn hàng đã thanh toán nhưng không còn key được reserve.');
            }

            $softwareKey->forceFill([
                'order_id' => $order->id,
                'status' => SoftwareKey::STATUS_DELIVERED,
                'delivered_at' => now(),
            ])->save();

            $order->forceFill([
                'software_key_id' => $softwareKey->id,
                'fulfillment_status' => Order::FULFILLMENT_FULFILLED,
                'fulfilled_at' => now(),
            ])->save();

            if ($order->customer_email) {
                try {
                    Mail::to($order->customer_email)->send(new OrderCredentialsMail($order->fresh(['package', 'softwareKey'])));
                } catch (\Throwable $exception) {
                    Log::warning('Unable to send order credential mail.', [
                        'order_number' => $order->order_number,
                        'message' => $exception->getMessage(),
                    ]);
                }
            }

            return $order->fresh(['package', 'softwareKey']);
        });
    }

    public function releaseExpiredReservations(): void
    {
        SoftwareKey::query()
            ->where('status', SoftwareKey::STATUS_RESERVED)
            ->whereHas('order', function ($query): void {
                $query
                    ->where('payment_status', Order::PAYMENT_PENDING)
                    ->where('expires_at', '<', now());
            })
            ->get()
            ->each(function (SoftwareKey $softwareKey): void {
                $softwareKey->forceFill([
                    'order_id' => null,
                    'status' => SoftwareKey::STATUS_AVAILABLE,
                    'reserved_at' => null,
                ])->save();

                $softwareKey->order?->forceFill([
                    'payment_status' => Order::PAYMENT_EXPIRED,
                    'software_key_id' => null,
                ])->save();
            });
    }

    protected function extractOrderNumberFromPayload(array $payload): ?string
    {
        $code = Arr::get($payload, 'code');

        if (is_string($code) && $code !== '') {
            return Str::upper(trim($code));
        }

        $haystack = trim((string) (Arr::get($payload, 'content') ?: Arr::get($payload, 'description')));

        if ($haystack === '') {
            return null;
        }

        preg_match('/(TVM[A-Z0-9]+)/i', $haystack, $matches);

        return isset($matches[1]) ? Str::upper($matches[1]) : null;
    }

    protected function hasValidWebhookAuthorization(Request $request): bool
    {
        $configured = trim((string) config('services.sepay.webhook_api_key'));

        if ($configured === '') {
            return true;
        }

        return hash_equals(
            'Apikey ' . $configured,
            (string) $request->header('Authorization')
        );
    }

    protected function generateOrderNumber(): string
    {
        do {
            $candidate = 'TVM' . now()->format('ymdHis') . Str::upper(Str::random(4));
        } while (Order::query()->where('order_number', $candidate)->exists());

        return $candidate;
    }
}
